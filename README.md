Guía rápida
--------------

1. Cargar BD
    ```
    Cargar fichero whatsapp.sql en un gesor de Base de Datos
    ```
    
2. Levantarproyecto whatsapp
   ```
    Poner en parameter.yml user y pass de Base de Datos
    
    Abrir un terminal dentro del proyecto y ejecutar: php app/console server:run
    ```
 
3. Abrir interfaz
   ```
    http://127.0.0.1:8000/admin
    User: admin  Pass:admin
   
    
    ```
php app/console doctrine:schema:validate para validar las relaciones de base de dato
