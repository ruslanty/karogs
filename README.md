# Karogs

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)

Karogs is a small PHP application that displays a calendar of recurring public holidays and selected commemoration days
and celebration days in Latvia. For the dates covered by the calendar, it indicates when the national flag of Latvia is
to be placed, including when it is to be placed in mourning presentation, and shows the flag-placement status for the
current day.

## Features

- Shows the current year and a calendar sorted by date. Dates use Latvian month names.
- Marks dates when the national flag of Latvia is to be placed on residential buildings. Mourning presentation is
  indicated where applicable.
- Shows whether the national flag of Latvia is to be placed today and, if so, whether it is to be placed in mourning
  presentation.
- Calculates recurring dates using the Gregorian calendar:
    - Easter Sunday using the Gregorian computus; Good Friday two days before Easter Sunday, Easter Monday the following
      day, and Pentecost 49 days after Easter Sunday;
    - Mother's Day as the second Sunday in May;
    - Commemoration Day of Victims of Genocide Against the Latvian People By the Totalitarian Communist Regime as the
      first Sunday in December.
- Uses the `Europe/Riga` time zone to determine the current date.

Transfers of working days are not taken into account.

## Flag display dates

The national flag of Latvia is to be placed on residential buildings on these recurring dates:

- 1 May, 4 May, 21 August, 11 November, and 18 November.
- In mourning presentation: 25 March, 14 June, 17 June, 4 July, and the first Sunday in December.

Other occasions stipulated by the Cabinet or local governments, flag placement on buildings where polling stations are
arranged on election and referendum days, and one-off public holidays are outside the current scope. The application
does not send reminders or messages.

## Run locally

Requirements: Docker and Docker Compose.

```sh
docker compose up --build
```

Open <http://127.0.0.1:8000/>.

To stop the application and remove its containers and network, run `docker compose down`.

The container runs PHP 8.5.12 with PHP's built-in web server. The document root is `public/`; the application code in
`src/` and the HTML template in `templates/` are outside it. The project has no Composer dependencies.

## Project structure

- `src/Domain/` - calendar-day model, flag-requirement and flag-decoration values, and the calendar interface.
- `src/Application/` - queries for the calendar schedule.
- `src/Infrastructure/Calendar/` - Latvian calendar implementation, including recurring dates and their calculations.
- `templates/` - Latvian-language HTML template, outside the document root.
- `public/index.php` - HTTP entry point and dependency wiring.
- `public/assets/` - browser assets.

## Official sources

- [Latvijas valsts karoga likums](https://likumi.lv/ta/id/200642-latvijas-valsts-karoga-likums)
- [Par svētku, atceres un atzīmējamām dienām](https://likumi.lv/ta/id/72608-par-svetku-atceres-un-atzimejamam-dienam)
