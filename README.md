Pasos a seguir para instalación
Correr en la consola los siguientes comandos:
- git clone https://github.com/chpaez18/challenge_horizm.git
- dentro de la carpeta del proyecto: composer update
- php artisan key:generate
- php artisan migrate
- php artisan serve

Para la primera parte, correr la siguiente ruta en postman o el navegador:
Method: GET
Path: /api/challenge/start

Revisar las tablas posts y users para verificar.

Para la segunda parte, tenemos los siguientes endpoints siguiendo las indicaciones mencionadas en la 2da parte de la prueba:
Method: GET
Path: /api/users

Method: GET
Path: /api/posts/top

Method: GET
Path: /api/posts/1
