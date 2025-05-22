<?php
namespace App\Controller;

use App\Repository\MenuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Mpdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MenuController extends AbstractController
{
    #[Route('/admin/menu', name: 'admin_menu')]
    public function adminMenu(Request $request, MenuRepository $menuRepo): Response
    {
        $session = $request->getSession();
        if (!$session->get('user_role') || !in_array('admin', $session->get('user_role'))) {
            return $this->redirectToRoute('login_form');
        }

        $filter = $request->query->get('filter', '');
        $menus = $menuRepo->getFiltered($filter);

        return $this->render('form.twig', [
            'menus' => $menus,
            'filter' => $filter,
        ]);
    }

    #[Route('/user/reports', name: 'user_reports')]
    public function userReports(Request $request): Response
    {
        $session = $request->getSession();
        if (!$session->get('user_role') || !in_array('user', $session->get('user_role'))) {
            return $this->redirectToRoute('login_form');
        }

        return $this->render('reports.twig');
    }

    #[Route('/menu/store', name: 'menu_store', methods: ['POST'])]
    public function store(Request $request, MenuRepository $menuRepo): Response
    {
        $fields = ['dish_name', 'ingredient1', 'ingredient2'];
        $data = [];
        $errors = [];

        foreach ($fields as $field) {
            $value = trim($request->request->get($field));
            $data[$field] = $value;
            if (empty($value) || !preg_match('/^[а-яА-ЯёЁa-zA-Z\s]+$/u', $value)) {
                $errors[] = "Введите корректное значение для $field.";
            }
        }

        $weight = trim($request->request->get('weight'));
        if (!ctype_digit($weight) || $weight < 1 || $weight > 5000) {
            $errors[] = "Введите допустимый вес блюда.";
        } else {
            $data['weight'] = (int)$weight;
        }

        if ($errors) {
            return new Response('<script>alert("' . implode('; ', $errors) . '"); window.location.href="/admin/menu";</script>');
        }

        $menuRepo->save($data['dish_name'], $data['ingredient1'], $data['ingredient2'], $data['weight']);

        return $this->render('success.twig');
    }

    #[Route('/export/pdf', name: 'export_pdf')]
    public function exportPdf(MenuRepository $menuRepo): Response
    {
        $data = $menuRepo->getAll();

        $mpdf = new Mpdf();

        $html = '<h1>Menu Report</h1><table border="1" cellpadding="5"><thead><tr><th>Блюдо</th><th>Ингредиент 1</th><th>Ингредиент 2</th><th>Вес</th></tr></thead><tbody>';
        foreach ($data as $menu) {
            $html .= sprintf(
                '<tr><td>%s</td><td>%s</td><td>%s</td><td>%d</td></tr>',
                htmlspecialchars($menu->getMenuDishName()),
                htmlspecialchars($menu->getMenuIngredient1()),
                htmlspecialchars($menu->getMenuIngredient2()),
                $menu->getMenuWeight()
            );
        }
        $html .= '</tbody></table>';

        $mpdf->WriteHTML($html);

        return new Response(
            $mpdf->Output('', 'S'),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="menu_report.pdf"',
            ]
        );
    }

    #[Route('/export/xlsx', name: 'export_xlsx')]
    public function exportXlsx(MenuRepository $menuRepo): Response
    {
        $data = $menuRepo->getAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Menu');

        // Заголовки столбцов
        $sheet->setCellValue('A1', 'Блюдо');
        $sheet->setCellValue('B1', 'Ингредиент 1');
        $sheet->setCellValue('C1', 'Ингредиент 2');
        $sheet->setCellValue('D1', 'Вес');

        $row = 2;
        foreach ($data as $menu) {
            $sheet->setCellValue("A$row", $menu->getMenuDishName());
            $sheet->setCellValue("B$row", $menu->getMenuIngredient1());
            $sheet->setCellValue("C$row", $menu->getMenuIngredient2());
            $sheet->setCellValue("D$row", $menu->getMenuWeight());
            $row++;
        }

        $writer = new Xlsx($spreadsheet);

        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="menu_report.xlsx"');

        return $response;
    }

    #[Route('/export/csv', name: 'export_csv')]
    public function exportCsv(MenuRepository $menuRepo): Response
    {
        $data = $menuRepo->getAll();

        $response = new StreamedResponse(function () use ($data) {
            $handle = fopen('php://output', 'w');

            fwrite($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            // Заголовки CSV
            fputcsv($handle, ['Блюдо', 'Ингредиент 1', 'Ингредиент 2', 'Вес']);

            foreach ($data as $menu) {
                fputcsv($handle, [
                    $menu->getMenuDishName(),
                    $menu->getMenuIngredient1(),
                    $menu->getMenuIngredient2(),
                    $menu->getMenuWeight()
                ]);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="menu_report.csv"');

        return $response;
    }
}