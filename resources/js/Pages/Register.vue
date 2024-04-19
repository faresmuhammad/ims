//TODO: Send Backend request to register user
//TODO: Redirect to home page
<template>

    <Head title="Sign Up" />
    <div
        class="surface-ground flex align-items-center justify-content-center min-h-screen min-w-screen overflow-hidden">
        <div class="flex flex-column align-items-center justify-content-center">
            <img src="https://blocks.primevue.org/images/blocks/logos/hyper.svg" alt="Sakai logo"
                class="mb-5 w-6rem flex-shrink-0" />
            <div
                style="border-radius: 56px; padding: 0.3rem; background: linear-gradient(180deg, var(--primary-color) 10%, rgba(33, 150, 243, 0) 30%)">
                <div class="w-full surface-card py-8 px-5 sm:px-8" style="border-radius: 53px">
                    <div class="text-center mb-5">
                        <div class="text-900 text-3xl font-medium mb-3">Inventory Management System</div>
                        <span class="text-600 font-medium text-3xl">Sign Up</span>
                    </div>

                    <div class="flex flex-column gap-4">
                        <div class="flex flex-column">
                            <label for="username" class="block text-900 text-xl font-medium mb-2">Username</label>
                            <InputText id="username" type="text" class="w-full md:w-30rem mb-2" style="padding: 1rem"
                                v-model="form.username" placeholder="Username"/>
                            <small class="p-error" v-if="errorMessage.username">{{ errorMessage.username }}</small>
                        </div>

                        <div class="flex flex-column">
                            <label for="email" class="block text-900 text-xl font-medium mb-2">Email</label>
                            <InputText id="email" type="email" class="w-full md:w-30rem mb-2" style="padding: 1rem" v-model="form.email"
                                :invalid="!errorMessage.validEmail" placeholder="Email"/>
                            <small class="p-error" v-if="errorMessage.email">{{ errorMessage.email }}</small>
                        </div>


                        <div class="flex flex-column">
                            <label for="password" class="block text-900 font-medium text-xl mb-2">Password</label>
                            <Password id="password" v-model="form.password" placeholder="Password" :toggleMask="true"
                                class="w-full md:w-30rem mb-3" inputClass="w-full" :inputStyle="{ padding: '1rem' }"></Password>
                        </div>

                        <div class="flex flex-column">
                            <label for="password-confirmation" class="block text-900 font-medium text-xl mb-2">Password Confirmation</label>
                            <Password id="password-confirmation" v-model="form.passwordConfirmation" placeholder="Password Confirmation" :toggleMask="true"
                                class="w-full md:w-30rem mb-2" inputClass="w-full" :inputStyle="{ padding: '1rem' }"></Password>
                            <small class="p-error" v-if="errorMessage.password">{{ errorMessage.password }}</small>
                        </div>

                        <Button @click="register" label="Sign Up" class="w-full p-3 text-xl"></Button>
                    </div>
                </div>
            </div>
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
