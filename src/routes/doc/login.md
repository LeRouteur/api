**User Login**
----
Calling this service permits to log in the user.

**POST body in form-encoded format.**

**DATA NEEDS TO BE SERIALIZED BEFORE POST !**

* **URL**

  https://secureconnect.online/api/auth/login

* **Method:**
  
  /api/auth/login

  `POST`

* **Data Params:**

    *Key => Value*

    `mail` => test@example.com

    `password` => SuperSecretP@ssword

* **Success Response:**

  * **Code:** 200 OK<br/>
    **Content:** `{"success":"Login Successful"}`
 
* **Error Response:**

  * **Code:** 401 Unauthorized<br/>
    **Content:** `{"error":"Username Or Password Is Incorrect"}`

  * **Code:** 400 Bad Request<br/>
    **Content:** `{"error":"Bad Request"}`

* **Sample Call:**

  ```javascript
    $.ajax({
      url: "https://secureconnect.online/api/auth/login",
      dataType: "json",
      type : "POST",
      data: {mail:"test@example.com", password:"SuperSecretP@ssword"},
      success : function(r) {
        console.log(r);
      }
    });
  ```

* **Notes:**

    Logging in creates a PHSESSID cookie used to store the session ID.