# Gendiff – Вычислитель отличий

[![project-check](https://github.com/StandAlone404/php-project-48/actions/workflows/project-check.yml/badge.svg)](https://github.com/StandAlone404/php-project-48/actions/workflows/project-check.yml)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=php-project-48&metric=alert_status)](https://sonarcloud.io/summary/new_code?id=php-project-48)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=php-project-48&metric=coverage)](https://sonarcloud.io/summary/new_code?id=php-project-48)
[![Code Smells](https://sonarcloud.io/api/project_badges/measure?project=php-project-48&metric=code_smells)](https://sonarcloud.io/summary/new_code?id=php-project-48)
[![Bugs](https://sonarcloud.io/api/project_badges/measure?project=php-project-48&metric=bugs)](https://sonarcloud.io/summary/new_code?id=php-project-48)
[![Duplicated Lines (%)](https://sonarcloud.io/api/project_badges/measure?project=php-project-48&metric=duplicated_lines_density)](https://sonarcloud.io/summary/new_code?id=php-project-48)
[![Lines of Code](https://sonarcloud.io/api/project_badges/measure?project=php-project-48&metric=ncloc)](https://sonarcloud.io/summary/new_code?id=php-project-48)
[![Reliability Rating](https://sonarcloud.io/api/project_badges/measure?project=php-project-48&metric=reliability_rating)](https://sonarcloud.io/summary/new_code?id=php-project-48)

---

## 📖 Описание

**Gendiff** — мощный инструмент для сравнения двух конфигурационных файлов в форматах `JSON` и `YAML`, с поддержкой вложенных структур и нескольких форматов вывода:

- `stylish` — древовидный формат (по умолчанию)
- `plain` — плоский человекочитаемый список изменений
- `json` — JSON-структура изменений

---

## 🎥 Демонстрации

Вывод в фомате stylish:

[![asciicast](https://asciinema.org/a/M5kRht39FKYV6LoFjISj92MKx.svg)](https://asciinema.org/a/M5kRht39FKYV6LoFjISj92MKx)

Вывод в фомате plain:

[![asciicast](https://asciinema.org/a/4hbI80NvSL7V521rtA02bTtMu.svg)](https://asciinema.org/a/4hbI80NvSL7V521rtA02bTtMu)

Вывод в фомате json:

[![asciicast](https://asciinema.org/a/SS6mt3J75lanThzt8WArdSXpq.svg)](https://asciinema.org/a/SS6mt3J75lanThzt8WArdSXpq)

---

## ⚙️ Установка

### Установка как глобальной утилиты:

```bash
git clone https://github.com/gennadiy-dev/php-project-48.git
cd php-project-48

make install # установка зависимостей

make link  # делает команду `gendiff` доступной глобально (Linux/macOS)
make link-wsl  # делает команду `gendiff` доступной глобально (Windows/Wsl)

make unlink  # удаляет ссылку (Linux/macOS)
make unlink-wsl  # удаляет ссылку с sudo для WSL (Windows/Wsl)
````

Подключение в коде:

```php
use function Differ\Differ\getDiff;

$diff = getDiff('file1.yaml', 'file2.json', 'plain');
echo $diff;
```

---

## 🛠 Использование в CLI

```bash
gendiff [--format <format>] <firstFile> <secondFile>
```

### Параметры:

| Параметр          | Описание                                   |
| ----------------- | ------------------------------------------ |
| `<firstFile>`     | Путь к первому файлу                       |
| `<secondFile>`    | Путь ко второму файлу                      |
| `--format`        | Формат вывода (`stylish`, `plain`, `json`) |
| `-h`, `--help`    | Показать справку                           |
| `-v`, `--version` | Показать версию                            |

---

## 💡 Примеры

### Stylish (по умолчанию):

```bash
gendiff file1.json file2.json
```

```diff
{
    common: {
      + follow: false
        setting1: Value 1
      - setting2: 200
      + setting2: 300
    }
}
```

### Plain:

```bash
gendiff --format plain file1.yaml file2.yaml
```

```text
Property 'common.follow' was added with value: false
Property 'common.setting2' was updated. From 200 to 300
```

### JSON:

```bash
gendiff --format json file1.json file2.json
```

```json
[
  {
    "key": "common",
    "status": "nested",
    "value": [...]
  }
]
```

---

## 🧪 Тестирование

```bash
make test
```

---

## 📂 Структура проекта

```text
bin
└── gendiff                 # Исполняемый файл CLI-интерфейса (входная точка утилиты)
src
├── Differ.php              # Основная логика: сравнение структур, построение дерева различий
├── FileReader.php          # Чтение и обработка входных файлов
├── Parsers.php             # Десериализация: парсинг JSON и YAML
├── Formatters.php          # Точка входа для форматтеров (стратегия выбора)
├── Formatters
│   ├── Helpers.php         # Вспомогательные функции форматирования
│   ├── Json.php            # Форматтер вывода в JSON-формате
│   ├── Plain.php           # Форматтер для вывода в "плоском" текстовом формате
│   └── Stylish.php         # Форматтер для дерева различий с отступами (по умолчанию)
└── enums
    └── Status.php          # Enum с перечислением статусов узлов: added, removed, updated и др.
```

---

## 🧑‍💻 Автор

[Gennadiy Maslov](https://github.com/gmaslov-dev)

---

## 📝 Лицензия

Проект распространяется под лицензией [MIT](LICENSE).
