**Show Information About A User**
----
Calling this service will return informations about a user by its mail.

* **URL**

  https://secureconnect.online/api/users/request

* **Method:**
  
  /api/users/request/email=test@example.com&auth_token=ageneratedtokenthatwillbegiventoyou123

  `GET`
  
*  **URL Params:** 

   **Required:**
 
   `email=[email]`
   -> Email of the user
   
   `auth_token=[ageneratedtokenthatwillbegiventoyou123]`
   -> Authentication token

* **Success Response:**

  * **Code:** 200 OK<br />
    **Content:** `[{"lastname":"Doe","firstname":"John","email":"test@example.com","sex":"man","address":"Living Dead 3","city":"Zombotron","postal_code":"111111","sub_status":"0"}]`
 
* **Error Response:**

  * **Code:** 400 Bad Request<br />
    **Content:** `{"error":"Bad request"}`

* **Sample Call:**

  ```javascript
    $.ajax({
      url: "/api/users/request/email=test@example.com&auth_token=ageneratedtokenthatwillbegiventoyou123",
      dataType: "json",
      type : "GET",
      success : function(r) {
        console.log(r);
      }
    });
  ```