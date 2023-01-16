# HOW TO RUN
Aby uruchomić lokalnie forum, jako host, musimy:
* Pobrać xampp: https://www.apachefriends.org/download.html
## Windows
* Po zainstalowaniu wchodzimy w : Apache -> Config -> httpd.conf i zmieniamy 
te dwie linijki np:
  * DocumentRoot "E:/WebdevProjects/inz-oprog-projekt/www" 
  * <Directory "E:/WebdevProjects/inz-oprog-projekt/www">

## Linux
Proces instalacji i konfiguracji może się różnić w zależności od wersji systemu. Nie ma sensu
opisywać każdej wersji - poradniki są ogólnodostępne w sieci.  
Poradnik dla ubuntu: https://phoenixnap.com/kb/how-to-install-xampp-on-ubuntu  
Omówię przypadek dla wersji __UBUNTU__.  
__Na potrzeby tutoriala, uznajmy, że katalog w którym zainstalowaliśmy __xampp-a__ to
defaultowy /opt/lampp__ (tak jak w poradniku dla ubuntu).  

* Dodajemy nasz projekt do katalogu __htdocs__ (katalog __htdocs__ jest w miejscu w którym zainstalowaliśmy xampp-a)
* Tak jak w przypadku windowsa, po zainstalowaniu musimy zmienic dwie linijki w pliku
__httpd.conf__ (__httpd.conf__ jest zlokalizowany w katalogu instalacyjnym xampp-a). Zamieniamy na:
  * DocumentRoot "/opt/lampp/htdocs/inz-oprog-projekt/www"
  * <Directory "/opt/lampp/htdocs/inz-oprog-projekt/www">
* Na koniec zostało uruchomić xampp-a:
  * sudo /opt/lampp/xampp start  
lub
  * Uruchamiamy okienkowy menadżer: sudo ./opt/lampp/manager-linux-<arch>.run
---
Dla obu przypadków:
* Uruchamiamy serwer Apache i Mysql: 
  * Apache -> start 
  * Mysql -> start
* Wpisujemy w przeglądarce: localhost
* Jeśli wszystko jest ok, wpisujemy w przeglądarce localhost/phpmyadmin
  * Tworzymy bazę danych o nazwie: __inz_oprog__
  * w panelu tworzenia bazy klikamy __import__ i wybieramy plik: __create_db.sql__ z
projektu.

Gotowe. Możemy korzystać z forum lokalnie :)