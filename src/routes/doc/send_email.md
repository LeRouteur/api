**Send Mail With Recovery Link**
----
Calling this service permits to send an email to the concerned user with a link that will allow him to change his password.

**POST body in form-encoded format.**

**DATA NEEDS TO BE SERIALIZED BEFORE POST !**

* **URL**

  https://secureconnect.online/api/auth/sendmail

* **Method:**
  
  /api/auth/sendmail

  `POST`

* **Data Params:**

    *Key => Value*

    `mail` => test@example.com

    `auth_token` => ageneratedtokenthatwillbegiventoyou123

* **Success Response:**

  * **Code:** 200 OK<br />
    **Content:** `{"success":"Mail Sent"}`
 
* **Error Response:**

  * **Code:** 400 Bad Request<br />
    **Content:** `{"error":"Bad Request"}`

  * **Code:** 500 Internal Server Error<br/>
    **Content:** `{"error":"Contact Administrator"}`

* **Sample Call:**

  ```javascript
    $.ajax({
      url: "/api/auth/sendmail",
      dataType: "json",
      type : "POST",
      data: {mail:"test@example.com", auth_token:"ageneratedtokenthatwillbegiventoyou123"},
      success : function(r) {
        console.log(r);
      }
    });
  ```

* **Notes:**

  Calling this service sends a mail to the concerned user, and should only be called when user will change his password/lost his password. The link in the mail will redirect to the frontend.