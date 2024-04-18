//TODO: Send Backend request to register user
//TODO: Redirect to home page
<template>

    <Head title="Sign Up" />
    <div class="flex align-content-center justify-content-center">
        <div class="surface-card p-4 shadow-2 border-round w-full lg:w-6">
            <div class="text-center mb-5">
                <img src="https://blocks.primevue.org/images/blocks/logos/hyper.svg" alt="Image" height="50"
                    class="mb-3" />
                <div class="text-900 text-3xl font-medium mb-3">Inventory Management System</div>
                <div class="text-900 text-3xl font-medium mb-3">Sign Up</div>
            </div>

            <form @submit.prevent>
                <div class="flex align-items-center gap-2">
                    <label for="username" class="block text-900 font-medium mb-3 w-2">Username</label>
                    <InputText id="username" type="text" class="w-6 mb-3" v-model="form.username" />
                    <InlineMessage class="mb-3 w-3" v-if="!form.username">Username is required</InlineMessage>
                </div>

                <div class="flex align-items-center gap-2">
                    <label for="email" class="block text-900 font-medium mb-2 w-2">Email</label>
                    <InputText id="email" type="email" class="w-6 mb-3" v-model="form.email"
                        :invalid="!errorMessage.validEmail" />
                    <InlineMessage class="mb-3 w-3" v-if="errorMessage.email">{{ errorMessage.email }}</InlineMessage>
                </div>
                <label for="password" class="block text-900 font-medium mb-2">Password</label>
                <div class="flex align-items-center gap-2">
                    <Password id="password" class="mb-3 w-6" v-model="form.password" toggleMask />
                </div>
                <label for="password-confirmation" class="block text-900 font-medium mb-2">Password Confirmation</label>
                <div class="flex align-items-center gap-2">
                    <Password id="password-confirmation" class="mb-3 w-6" v-model="form.passwordConfirmation" toggleMask
                        :invalid="!errorMessage.passwordConfirmed" />
                    <InlineMessage class="mb-3 w-3" v-if="errorMessage.password">{{ errorMessage.password }}
                    </InlineMessage>
                </div>

                <Button @click="register" label="Sign Up" class="w-full text-2xl"></Button>
            </form>
        </div>
    </div>
</template>


<script setup>
import { Head } from '@inertiajs/vue3';
import Password from 'primevue/password';
import { reactive } from 'vue';
const form = reactive({
    username: '',
    email: '',
    password: '',
    passwordConfirmation: ''
})
const errorMessage = reactive({
    email: '',
    validEmail: true,
    username: '',
    validUsername: true,
    password: '',
    passwordConfirmed: true
})

const validUsername = () => {
    //TODO: validate the username is unique
}

const validateEmail = () => {
    if (!form.email) {
        errorMessage.email = 'Email is required'
        errorMessage.validEmail = false
    } else {
        errorMessage.email = ''
        errorMessage.validEmail = true
        const regex = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
        if (!regex.test(form.email)) {
            errorMessage.email = 'Invalid Email'
            errorMessage.validEmail = false
        }
        else {
            errorMessage.email = ''
            errorMessage.validEmail = true
        }
    }
}
const passwordMatching = () => {
    if (form.password !== form.passwordConfirmation) {
        errorMessage.password = 'Passwords do not match'
        errorMessage.passwordConfirmed = false
    }
    else {
        errorMessage.password = ''
        errorMessage.passwordConfirmed = true

    }
}
const validate = () => {
    validateEmail();
    passwordMatching();
}
const register = () => {
    validate()
    console.log(form)
}
</script>
