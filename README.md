## Description

```bash
# Запустить команду для полного разворачивания проекта
$ make init
```
```bash
# Запуск cbr сравнения с предыдущим днем
URL: GET http://localhost/api/currency/rate 
BODY {
   "date": "2024-05-08",
   "currency": "USD"
}

# Запуск cbr за 180 предыдущих дней
# Файл сохраниться в папке public
# Воркер уже запущен в докере
URL: GET http://localhost/api/currency/collect
BODY {
   "currency": "USD"
}
```
