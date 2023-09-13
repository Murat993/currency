## Description

```bash
# Запустить команду для полного разворачивания проекта
$ make init
```
```bash
# Запуск cbr сравнения с предыдущим днем
# Возможно нужны будут права для папки storage
URL: GET http://localhost/api/currency/rate 
BODY {
   "date": "2023-09-13",
   "currency": "USD"
}

# Запуск cbr за 180 предыдущих дней
# Сохранится файл в папке public
# Воркер уже запущен в докере
URL: GET http://localhost/api/currency/collect
BODY {
   "currency": "USD"
}
```
