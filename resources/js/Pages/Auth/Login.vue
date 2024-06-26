<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";

// コンポーネント
import AuthenticationCard from "@/Components/AuthenticationCard.vue";
import AuthenticationCardLogo from "@/Components/AuthenticationCardLogo.vue";
import Checkbox from "@/Components/Checkbox.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";

// バリデーション
import { isValidEmail, isValidPassword } from "@/Utils/validators";

defineProps({
  canResetPassword: Boolean,
  status: String,
});

const form = useForm({
  email: "",
  password: "",
  remember: false,
});

const submit = () => {
  form
    .transform((data) => ({
      ...data,
      remember: form.remember ? "on" : "",
    }))
    .post(route("login"), {
      onFinish: () => form.reset("password"),
    });
};

// バリデーション
const validEmail = () => {
  const { isValid, errorMessage } = isValidEmail(form.email);
  if (!isValid) {
    form.errors.email = errorMessage;
  } else {
    form.errors.email = "";
  }
};

const validPassword = () => {
  const { isValid, errorMessage } = isValidPassword(form.password);
  if (!isValid) {
    form.errors.password = errorMessage;
  } else {
    form.errors.password = "";
  }
};
</script>

<template>
  <Head title="Log in" />

  <AuthenticationCard>
    <template #logo>
      <AuthenticationCardLogo />
    </template>

    <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
      {{ status }}
    </div>

    <form @submit.prevent="submit" class="">
      <div>
        <InputLabel for="email" value="Email" class="text-white" />
        <TextInput
          id="email"
          v-model="form.email"
          type="email"
          class="mt-1 block w-full"
          required
          autofocus
          autocomplete="username"
          @blur="validEmail"
        />
        <InputError class="mt-2" :message="form.errors.email" />
      </div>

      <div class="mt-4">
        <InputLabel for="password" value="Password" class="text-white" />
        <TextInput
          id="password"
          v-model="form.password"
          type="password"
          class="mt-1 block w-full"
          required
          autocomplete="current-password"
          @blur="validPassword"
        />
        <InputError class="mt-2" :message="form.errors.password" />
      </div>

      <div class="block mt-4">
        <label class="flex items-center">
          <Checkbox v-model:checked="form.remember" name="remember" />
          <span class="ml-2 text-sm text-white">次回から自動ログイン</span>
        </label>
      </div>

      <div class="flex items-center justify-end mt-4">
        <Link
          v-if="canResetPassword"
          :href="route('password.request')"
          class="underline text-sm hover:text-gray-400 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 text-white"
        >
          パスワードを忘れましたか？
        </Link>

        <PrimaryButton
          class="ml-4 border-white"
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
        >
          ログイン
        </PrimaryButton>
      </div>

      <div class="flex items-center justify-end mt-4">
      <Link
          v-if="canResetPassword"
          :href="route('register')"
          class="underline text-sm hover:text-gray-400 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 text-white"
        >
          新規登録はこちら
        </Link>
        </div>
    </form>
  </AuthenticationCard>
</template>
