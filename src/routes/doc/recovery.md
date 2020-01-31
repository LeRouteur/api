**Change User Password**
----
Calling this service permits to modify user password.

**POST body in form-encoded format.**

**DATA NEEDS TO BE SERIALIZED BEFORE POST !**

* **URL**

  https://secureconnect.online/api/users/recovery

* **Method:**
  
  /api/users/recovery/email=test@example.com&token=5875db50d38c2aa8e21829955e7804218d3af327

  `POST`

*  **URL Params:** 

   **Required:**
 
   `email=[email]`
   -> Email of the user
   
   `token=[activationtokensupersecret1234]`
   -> User token

* **Data Params:**

    *Key => Value*

    `password` => SuperSecretP@ssword123

    `password_conf` => SuperSecretP@ssword123

    `auth_token` => ageneratedtokenthatwillbegiventoyou123

* **Success Response:**

  * **Code:** 200 OK<br />
    **Content:** `{"success":"Password Modified"}`
 
* **Error Response:**

  * **Code:** 400 Bad Request<br />
    **Content:** `{"error":"Bad Request"}`
    
  * **Code:** 400 Bad Request<br />
    **Content:** `{"error":"Password Does Not Meet The Requirements"}`

  * **Code:** 401 Unauthorized<br/>
    **Content:** `{"error":"Password Does Not Match"}`

  * **Code:** 500 Internal Server Error<br/>
    **Content:** `{"error":"Contact Administrator"}`

* **Sample Call:**

  ```javascript
    $.ajax({
      url: "https://secureconnect.online/api/users/recovery/email=test@example.com&token=activationtokensupersecret1234",
      dataType: "json",
      type : "POST",
      data:{password:"SuperSecretP@ssword123", password_conf:"SuperSecretP@ssword123", auth_token:"ageneratedtokenthatwillbegiventoyou123"},
      success : function(r) {
        console.log(r);
      }
    });
  ```

* **Notes:**

  This service needs the URL params AND the body to work. The URL params are given in the email that has been sent to the user.