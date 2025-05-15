document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("menuForm").addEventListener("submit", function (event) {
        let isValid = true;
        let errors = [];

        function validateTextField(id, fieldName) {
            const field = document.getElementById(id);
            const value = field.value.trim();
            const regex = /^[а-яА-ЯёЁa-zA-Z\s]+$/u;

            if (value === "") {
                errors.push(`Поле "${fieldName}" не может быть пустым.`);
                isValid = false;
            } else if (!regex.test(value)) {
                errors.push(`"${fieldName}" должно содержать только буквы и пробелы.`);
                isValid = false;
            }
        }

        function validateWeightField(id, fieldName) {
            const field = document.getElementById(id);
            const value = field.value.trim();
            const weight = parseInt(value, 10);

            if (value === "" || isNaN(weight)) {
                errors.push(`Поле "${fieldName}" должно быть числом.`);
                isValid = false;
            } else if (weight < 1 || weight > 5000) {
                errors.push(`Поле "${fieldName}" должно быть от 1 до 5000 грамм.`);
                isValid = false;
            }
        }

        validateTextField("dish_name", "Название блюда");
        validateTextField("ingredient1", "Первый ингредиент");
        validateTextField("ingredient2", "Второй ингредиент");
        validateWeightField("weight", "Вес блюда");

        if (!isValid) {
            alert(errors.join("\n"));
            event.preventDefault();
        }
    });
});
