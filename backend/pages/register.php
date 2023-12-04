<?php
// if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])){
//     header('Location: /');
// }
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Backend</title>
    <base href="/">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="./favicon.ico">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/register.css">
    <link rel="stylesheet" href="./css/event-modal.css">
    <link rel="stylesheet" href="./css/toggle-theme-switch.css">
    <link rel="stylesheet" href="./css/navigation-panel.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

</head>

<body>
    <?php
    include_once "./components/NavigationPanel.php";
    ?>
    <div class="registration">
        <div class="registration-form">
            <h2>Registration</h2>
            <form action ="./scripts/register.php" method="POST" class="form">
                <div class="form-group">
                    <label for="email">Username:</label>
                    <input type="text" id="username" name="username" @input="validateUsername" v-model="username" required />
                    <div v-color:red v-if="errors.username" class="invalid-input-error">
                        {{ errors.username }}
                    </div>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" @input="validateEmail" v-model="email" required />
                    <div v-color:red v-if="errors.email" class="invalid-input-error">
                        {{ errors.email }}
                    </div>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" @input="validatePassword" v-model="password" required />
                    <div v-color:red v-if="errors.password" class="invalid-input-error">
                        {{ errors.password }}
                    </div>
                </div>
                <div class="error" v-if="error">{{ error }}</div>
                <button type="submit" class="register-button">Register</button>
            </form>
            <button @click="signInWithGoogleHandler" class="google-button">
                Register with Google
            </button>
            <div>
                Already have an account?
                <router-link :to="{name: 'login'}"> Login</router-link>
            </div>
        </div>
        <modal-message v-if="isModalVisible">
            <template v-slot:header>
                <h2>Last one step</h2>
            </template>
            <template v-slot:content>
                <p>
                    Click the link we sent to {{ email }} to complete your account set-up.
                </p>
            </template>
            <template v-slot:buttons>
                <button class="message-button" @click="closeModal">Got It!</button>
            </template>
        </modal-message>
    </div>
</body>

</html>