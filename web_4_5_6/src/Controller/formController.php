<?php
namespace App\Controller;

use App\Model\Menu;
use App\Model\User;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Mpdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class FormController {
    private Environment $twig;
    private Menu $modelMenu;
    private User $userModel;

    public function __construct() {
        $loader = new FilesystemLoader(__DIR__ . '/../../templates');
        $this->twig = new Environment($loader);
        $this->modelMenu = new Menu();
        $this->userModel = new User();
    }

    public function registerForm() {
        echo $this->twig->render('register.twig');
    }

    public function register() {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($password === $confirmPassword) {
            if ($this->userModel->register($username, $password)) {
                header('Location: /');
                exit;
            } else {
                echo "Ошибка регистрации.";
            }
        } else {
            echo "Пароли не совпадают.";
        }
    }

    public function loginForm() {
        echo $this->twig->render('login.twig');
    }

    public function adminMenu() {
        if ($_SESSION['user_role'] !== 'admin') {
            header('Location: /');
            exit;
        }
        echo $this->twig->render('form.twig');
    }

    public function userReports() {
        if ($_SESSION['user_role'] !== 'user') {
            header('Location: /');
            exit;
        }

        echo $this->twig->render('reports.twig'); // шаблон с кнопками "Скачать PDF / CSV / XLSX"
    }


    public function login() {
        $username = $_POST['user_login'] ?? '';
        $password = $_POST['user_password'] ?? '';

        $user = $this->userModel->findByLogin($username);
        if ($user && $this->userModel->verifyPassword($password, $user['user_password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_login'] = $user['user_login'];
            $_SESSION['user_role'] = $user['user_role'];
            if ($_SESSION['user_role'] === 'admin') {
                header('Location: /admin/menu');
            } elseif ($_SESSION['user_role'] === 'user') {
                header('Location: /user/reports');
            } else {
                header('Location: /');
            }
            exit;
        } else {
            echo "Неверный логин или пароль.";
        }
    }
    public function logout() {
        session_destroy();
        header("Location: /");
        exit;
    }
    
    public function exportPdf() {
        $data = $this->modelMenu->getAll();

        $mpdf = new Mpdf([
            'tempDir' => __DIR__ . '/../../vendor/mpdf/mpdf/tmp',
            'default_font' => 'dejavusans',
        ]);

        $html = '<h1>Отчет по меню</h1>';
        $html .= '<table border="1" cellpadding="5" cellspacing="0">';
        $html .= '<tr><th>Название блюда</th><th>Ингредиент 1</th><th>Ингредиент 2</th><th>Вес (г)</th></tr>';

        foreach ($data as $row) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($row['dish_name']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row['ingredient1']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row['ingredient2']) . '</td>';
            $html .= '<td>' . $row['weight'] . '</td>';
            $html .= '</tr>';
        }

        $html .= '</table>';

        $mpdf->WriteHTML($html);
        $mpdf->Output('menu_report.pdf', 'D');
    }

    public function exportCsv() {
        $data = $this->modelMenu->getAll();

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="report.csv"');

        $output = fopen('php://output', 'w');

        fwrite($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        fputcsv($output, ['Название блюда', 'Ингредиент 1', 'Ингредиент 2', 'Вес']);

        foreach ($data as $row) {
            fputcsv($output, [$row['dish_name'], $row['ingredient1'], $row['ingredient2'], $row['weight']]);
        }

        fclose($output);
        exit;
    }


    public function exportXlsx() {
        $data = $this->modelMenu->getAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray(['Название', 'Ингредиент 1', 'Ингредиент 2', 'Вес'], NULL, 'A1');

        $row = 2;
        foreach ($data as $item) {
            $sheet->setCellValue("A$row", $item['dish_name']);
            $sheet->setCellValue("B$row", $item['ingredient1']);
            $sheet->setCellValue("C$row", $item['ingredient2']);
            $sheet->setCellValue("D$row", $item['weight']);
            $row++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="report.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    // public function index() {
    //     $users = $this->modelMenu->getAll();
    //     echo $this->twig->render('form.twig', ['users' => $users]);
    // }

    public function index() {
        $filter = $_GET['filter'] ?? '';
        $menus = $this->modelMenu->getFiltered($filter);

        echo $this->twig->render('form.twig', [
            'users' => $menus,
            'filter' => $filter
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fields = [
                'dish_name' => 'Название блюда',
                'ingredient1' => 'Первый ингредиент',
                'ingredient2' => 'Второй ингредиент'
            ];

            $errors = [];
            $data = [];

            foreach ($fields as $field => $label) {
                $value = htmlspecialchars(trim($_POST[$field] ?? ''));
                $data[$field] = $value;

                if (empty($value) || !preg_match('/^[а-яА-ЯёЁa-zA-Z\s]+$/u', $value)) {
                    $errors[] = "Введите корректное значение для поля: $label.";
                }
            }

            $weight = htmlspecialchars(trim($_POST['weight'] ?? ''));
            if (!ctype_digit($weight) || (int)$weight < 1 || (int)$weight > 5000) {
                $errors[] = "Введите допустимый вес блюда (от 1 до 5000 грамм).";
            } else {
                $data['weight'] = (int)$weight;
            }

            if(empty($errors)){
                $this->modelMenu->save($data['dish_name'], $data['ingredient1'], $data['ingredient2'], $data['weight']);

                echo $this->twig->render('success.twig');
            }
            else{
                // echo $this->twig->render('failure.twig');
                $message = implode('; ', $errors);
                echo "<script>alert('" . $message . "'); window.location.href='/';</script>";
            }
        }
    }
}
