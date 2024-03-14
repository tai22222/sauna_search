<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

// バリデーション
import { isValidText, isValidEmail, isValidPassword, doPasswordsMatch } from '@/utils/validators';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

// バリデーション
const validText = (max, min) => {
  const { isValid, errorMessage } = isValidText(form.name, max, min);
  if(!isValid){
    form.errors.name = errorMessage;
  } else {
    form.errors.name = "";
  }
}

const validEmail = () => {
  const { isValid, errorMessage } = isValidEmail(form.email);
  if(!isValid) {
    form.errors.email = errorMessage;
  } else {
    form.errors.email = "";
  }
}

const validPassword = () => {
  const { isValid, errorMessage} = isValidPassword(form.password);
  if(!isValid) {
    form.errors.password = errorMessage;
  } else {
    form.errors.password = "";
  }
}

const confirmPassword = () => {
  const { isValid, errorMessage } = doPasswordsMatch(form.password ,form.password_confirmation);
  if(!isValid){
    form.errors.password_confirmation = errorMessage;
  } else {
    form.errors.password_confirmation = "";
  }
}
</script>

<template>
    <Head title="ユーザー登録" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="ユーザーネーム" class="text-white" />
                <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autofocus
                    autocomplete="name"
                    @blur="() => validText(50, 0)"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="メールアドレス" class="text-white" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    autocomplete="username"
                    @blur="validEmail"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="パスワード" class="text-white" />
                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="new-password"
                    @blur="validPassword"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="パスワード(確認用)" class="text-white" />
                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="new-password"
                    @blur="confirmPassword"
                />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature" class="mt-4">
                <InputLabel for="terms">
                    <div class="flex items-center text-white">
                        <Checkbox id="terms" v-model:checked="form.terms" name="terms" required />

                        <div class="ml-2">
                            I agree to the <a target="_blank" :href="route('terms.show')" class="underline text-sm text-gray-300 hover:text-gray-400 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Terms of Service</a> and <a target="_blank" :href="route('policy.show')" class="underline text-sm text-gray-300 hover:text-gray-400 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Privacy Policy</a>
                        </div>
                    </div>
                    <InputError class="mt-2" :message="form.errors.terms" />
                </InputLabel>
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link :href="route('login')" 
                      class="underline text-sm text-white hover:text-gray-400 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    すでにアカウントをお持ちですか？
                </Link>

                <PrimaryButton class="ml-4 border-white"
                               :class="{ 'opacity-25': form.processing }"
                               :disabled="form.processing">
                    登録
                </PrimaryButton>
            </div>
        </form>
    </AuthenticationCard>
</template>
