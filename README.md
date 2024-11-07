# Petstore Project

## Opis projektu
Petstore Project to aplikacja webowa napisana w frameworku Laravel, umożliwiająca zarządzanie danymi zwierząt poprzez interakcję z zewnętrznym API Petstore (https://petstore.swagger.io/). Aplikacja pozwala na dodawanie, edytowanie, wyświetlanie oraz usuwanie informacji o zwierzętach, w tym obsługę zdjęć oraz tagów. Projekt jest przykładem praktycznego wykorzystania wzorca MVC (Model-View-Controller) oraz integracji z zewnętrznymi usługami za pomocą API.

## Funkcjonalności
- **Dodawanie nowego zwierzęcia**: Formularz umożliwiający wprowadzenie danych, w tym nazwę, kategorię, status, tagi oraz dodanie zdjęcia.
- **Edycja zwierzęcia**: Formularz edycji danych zwierzęcia z możliwością zmiany istniejącego zdjęcia (z automatycznym usuwaniem starego).
- **Wyświetlanie szczegółów zwierzęcia**: Widok szczegółowy z informacjami o zwierzęciu i jego zdjęciach.
- **Lista zwierząt**: Strona wyświetlająca wszystkie zwierzęta dostępne w systemie.
- **Usuwanie zwierzęcia**: Możliwość usunięcia zwierzęcia wraz z powiązanym zdjęciem.

## Instalacja
1. **Sklonuj repozytorium**:
   ```bash
   git clone https://github.com/EugeneBachura/petstore_project.git
   cd petstore_project
   ```

2. **Zainstaluj zależności za pomocą Composer**:
   ```bash
   composer install
   ```

3. **Skonfiguruj plik `.env`**:
   Utwórz plik `.env` na podstawie pliku `.env.example` i skonfiguruj połączenie z bazą danych oraz inne ustawienia aplikacji.

4. **Wygeneruj klucz aplikacji**:
   ```bash
   php artisan key:generate
   ```

5. **Utwórz link do katalogu z obrazami**:
   ```bash
   php artisan storage:link
   ```

## Ustawienia uprawnień
Aby zapewnić poprawne działanie aplikacji, upewnij się, że prawa do plików i katalogów są prawidłowe:

1. **Ustawienie praw do zapisu dla katalogów**:
   ```bash
   sudo chown -R www-data:www-data storage
   sudo chmod -R 775 storage
   sudo chown -R www-data:www-data bootstrap/cache
   sudo chmod -R 775 bootstrap/cache
   ```

2. **Upewnij się, że pliki logów są dostępne dla użytkownika serwera**:
   ```bash
   sudo chown -R www-data:www-data storage/logs
   sudo chmod -R 775 storage/logs
   ```

## **Testowanie aplikacji**:
Przez pewien czas po publikacji aplikacja będzie dostępna do testowania pod adresem:
http://70.34.252.228/pets

## Używanie aplikacji
1. **Uruchom serwer lokalny**:
   ```bash
   php artisan serve
   ```
2. **Otwórz przeglądarkę i przejdź do adresu**:
   ```
   http://localhost:8000
   ```

## Struktura aplikacji
Aplikacja składa się z następujących kluczowych elementów:
- **Kontrolery**: Obsługa logiki aplikacji i komunikacja z serwisami (np. `PetController`).
- **Usługi**: Integracja z zewnętrznym API Petstore (np. `PetService`).
- **Widoki**: Szablony Blade do prezentacji danych użytkownikowi (np. `pets.create`, `pets.edit`).

## Uwagi dodatkowe
- **Obsługa błędów**: Aplikacja obsługuje błędy w przypadku niepowodzenia komunikacji z API i wyświetla odpowiednie komunikaty.
- **Ochrona przed XSS**: Dane tekstowe są ekranowane przed wyświetleniem, aby zapobiec atakom typu XSS.


