**User Subscription**
----
Calling this service permits to change user subscription status.

**POST body in form-encoded format.**

**DATA NEEDS TO BE SERIALIZED BEFORE POST !**

* **URL**

  https://secureconnect.online/api/user/subscription

* **Method:**

  `POST`

* **Data Params:**

    *Key => Value*
    
    `status` => 1
    
    `auth_token` => ageneratedtokenthatwillbegiventoyou123

    `mail` => test@example.com
    
    `username` => johndoe

* **Success Response:**

  * **Code:** 200 OK<br/>
    **Content:** `{"success":"Status Updated"}`
 
* **Error Response:**

  * **Code:** 400 Bad Request<br/>
    **Content:** `{"error":"User Already In Group"}`

  * **Code:** 500 Internal Server Error<br/>
    **Content:** `{"error":"Contact Administrator"}`

* **Sample Call:**

  ```javascript
    $.ajax({
      url: "https://secureconnect.online/api/user/update",
      dataType: "json",
      type : "POST",
      data: {status:"3", auth_token:"ageneratedtokenthatwillbegiventoyou123", mail:"test@example.com", username:"john.doe"},
      success : function(r) {
        console.log(r);
      }
    });
  ```

* **Notes:**

    This service also adds the user in the correct AD group.
    You need to have the auth_token to update the subscription type of a user. If you loose it, please contact the administrator.