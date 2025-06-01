# pizza-place

#backend
step 1: goto backend
step 2: create your database
step 3: put database details to the .env file
step 4: set your local backend api server name to pizza-place-api.test
step 5: run composer install
step 6: run php artisan migrate:fresh --seed

frontend
step 1: goto frontend
step 2: goto frontend\pizza-place\src\views\OrderView.vue and change the api url to the one  you setup, do it also to PizzaView.vue
step 3: run npm install
step 4: npm run build