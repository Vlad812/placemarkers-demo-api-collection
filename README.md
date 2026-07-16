> Этот сервис: `api-placemarkers-collection` является частью приложения [placemarkers-demo-workstation](https://github.com/Vlad812/placemarkers-demo-workstation).

# API Documentation: Placemarkers Collection

**Стек технологий:**
- PHP 8.5
- Symfony 8
- RoadRunner
- MongoDB
- Doctrine MongoDB ODM

Сервис для работы с пользовательскими коллекциями гео-меток. Позволяет сохранять подборки вместе с критериями поиска (координаты, радиус) и снимками меток на момент сохранения, а также просматривать и удалять свои коллекции. Данные хранятся в **MongoDB** через **Doctrine MongoDB ODM** (документы, `DocumentManager`, fetcher для чтения списка коллекций).

**Авторизация (общее):** Bearer Token (JWT). Пользователь видит и управляет только своими коллекциями.

**Формат ошибок (общий):**

```json
{
  "errors": [
    {
      "message": "Текст ошибки"
    }
  ]
}
```

---

## Создание коллекции (Create Collection)

Сохраняет подборку меток с критериями поиска, по которым она была собрана.

**URL:** `/collections`  
**Метод:** `POST`  
**Авторизация:** Требуется (Bearer Token)

### Request (Запрос)

#### Заголовки (Headers)
* `Content-Type: application/json`
* `Authorization: Bearer <token>`

#### Тело запроса (JSON Body)

| Поле | Тип | Обязательное | Описание | Пример |
| :--- | :--- | :---: | :--- | :--- |
| `name` | `string` | Нет | Название коллекции. По умолчанию `"Unnamed Collection"`. | `"Подборка"` |
| `search_criteria` | `object` | **Да** | Критерии поиска, по которым собрана коллекция. | |
| `search_criteria.latitude` | `number` | **Да** | Широта центра поиска. | `59.94` |
| `search_criteria.longitude` | `number` | **Да** | Долгота центра поиска. | `30.31` |
| `search_criteria.radius` | `number` | **Да** | Радиус поиска в метрах. | `1000` |
| `placemarkers` | `array` | Нет | Список снимков меток. По умолчанию пустой массив. | |
| `placemarkers[].originalId` | `string` | **Да*** | Идентификатор исходной метки. Альтернатива: `id`. | `"123e4567-..."` |
| `placemarkers[].title` | `string` | **Да*** | Название метки. Альтернатива: `name`. | `"Любимая кофейня"` |
| `placemarkers[].latitude` | `number` | **Да*** | Широта. Альтернатива: `lat`. | `59.94` |
| `placemarkers[].longitude` | `number` | **Да*** | Долгота. Альтернатива: `lon`. | `30.31` |
| `placemarkers[].description` | `string\|null` | Нет | Описание метки. | `"Отличный кофе"` |
| `placemarkers[].typeId` | `string` | Нет | Тип метки. Альтернатива: `type_id`. По умолчанию `default`. | `"cafe"` |
| `placemarkers[].tags` | `array<string>` | Нет | Теги метки. | `["coffee"]` |

\* Обязательно для каждого элемента массива `placemarkers`, если массив передан.

#### Пример запроса

```http
POST /collections HTTP/1.1
Content-Type: application/json
Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
```

```json
{
  "name": "Подборка",
  "search_criteria": {
    "latitude": 59.94,
    "longitude": 30.31,
    "radius": 1000
  },
  "placemarkers": [
    {
      "originalId": "123e4567-e89b-12d3-a456-426614174000",
      "title": "Любимая кофейня",
      "latitude": 59.94,
      "longitude": 30.31,
      "description": "Отличный кофе",
      "typeId": "cafe",
      "tags": ["coffee"]
    }
  ]
}
```

### Responses (Ответы)

#### 🟢 201 Created — Успешное создание

Коллекция сохранена в MongoDB.

```json
{
  "status": "success",
  "collection_id": "674a1b2c3d4e5f6789012345"
}
```

#### 🔴 400 Bad Request — Некорректный запрос

Возвращается при синтаксических ошибках в JSON.

```json
{
  "errors": [
    {
      "message": "Invalid JSON."
    }
  ]
}
```

#### 🔴 401 Unauthorized — Ошибка авторизации

Возвращается, если пользователь не авторизован (отсутствует или недействителен токен).

#### 🔴 422 Unprocessable Entity — Ошибка валидации данных

```json
{
  "errors": [
    {
      "message": "Missing required parameter: search_criteria"
    }
  ]
}
```

```json
{
  "errors": [
    {
      "message": "Missing required parameter: search_criteria.latitude"
    }
  ]
}
```

```json
{
  "errors": [
    {
      "message": "Placemarker at index 0 must have originalId or id"
    }
  ]
}
```

#### 🔴 500 Internal Server Error — Внутренняя ошибка

```json
{
  "errors": [
    {
      "message": "Internal server error."
    }
  ]
}
```

---

## Список коллекций пользователя (Get User Collections)

Возвращает все коллекции текущего авторизованного пользователя.

**URL:** `/collections`  
**Метод:** `GET`  
**Авторизация:** Требуется (Bearer Token)

### Request (Запрос)

#### Заголовки (Headers)
* `Authorization: Bearer <token>`

#### Пример запроса

```http
GET /collections HTTP/1.1
Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
```

### Responses (Ответы)

#### 🟢 200 OK — Успешный ответ

Сортировка: по `createdAt DESC`. Если коллекций нет — `data` будет пустым массивом.

```json
{
  "status": "success",
  "data": [
    {
      "id": "674a1b2c3d4e5f6789012345",
      "name": "Подборка",
      "createdAt": "2026-07-13T10:30:00+00:00",
      "searchCriteria": {
        "latitude": 59.94,
        "longitude": 30.31,
        "radiusMeters": 1000
      },
      "placemarkersCount": 1,
      "placemarkers": [
        {
          "originalId": "123e4567-e89b-12d3-a456-426614174000",
          "title": "Любимая кофейня",
          "latitude": 59.94,
          "longitude": 30.31,
          "typeId": "cafe",
          "description": "Отличный кофе",
          "tags": ["coffee"]
        }
      ]
    }
  ]
}
```

#### 🔴 401 Unauthorized — Ошибка авторизации

Возвращается, если пользователь не авторизован (отсутствует или недействителен токен).

#### 🔴 500 Internal Server Error — Внутренняя ошибка

```json
{
  "errors": [
    {
      "message": "Internal server error."
    }
  ]
}
```

---

## Удаление коллекции (Delete Collection)

Удаляет коллекцию по идентификатору. Доступно только владельцу коллекции.

**URL:** `/collections/{id}`  
**Метод:** `DELETE`  
**Авторизация:** Требуется (Bearer Token)

### Request (Запрос)

#### Заголовки (Headers)
* `Authorization: Bearer <token>`

#### Параметры пути (Path Parameters)

| Параметр | Тип | Обязательное | Описание | Пример |
| :--- | :--- | :---: | :--- | :--- |
| `id` | `string` | **Да** | Идентификатор коллекции (MongoDB ObjectId). | `"674a1b2c3d4e5f6789012345"` |

#### Пример запроса

```http
DELETE /collections/674a1b2c3d4e5f6789012345 HTTP/1.1
Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
```

### Responses (Ответы)

#### 🟢 200 OK — Успешное удаление

```json
{
  "status": "success"
}
```

#### 🔴 401 Unauthorized — Ошибка авторизации

Возвращается, если пользователь не авторизован (отсутствует или недействителен токен).

#### 🔴 422 Unprocessable Entity — Ошибка валидации / доступа

Коллекция не найдена или принадлежит другому пользователю.

```json
{
  "errors": [
    {
      "message": "Collection not found"
    }
  ]
}
```

```json
{
  "errors": [
    {
      "message": "Access denied"
    }
  ]
}
```

```json
{
  "errors": [
    {
      "message": "Missing required parameter: id"
    }
  ]
}
```

#### 🔴 500 Internal Server Error — Внутренняя ошибка

```json
{
  "errors": [
    {
      "message": "Internal server error."
    }
  ]
}
```

---

## Проверка состояния сервиса (Health Check)

Проверяет доступность сервиса. Используется для мониторинга и оркестрации (Docker, Kubernetes).

**URL:** `/health`  
**Метод:** `GET`  
**Авторизация:** Не требуется

### Request (Запрос)

#### Пример запроса

```http
GET /health HTTP/1.1
```

### Responses (Ответы)

#### 🟢 200 OK — Сервис доступен

```json
{
  "status": "ok"
}
```
