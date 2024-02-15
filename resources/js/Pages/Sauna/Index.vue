<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, computed, defineProps } from 'vue';

import { Link, router, useForm, usePage } from '@inertiajs/vue3';

import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Textarea from '@/Components/Textarea.vue';
import SelectBox from '@/Components/SelectBox.vue';

// 親コンポーネント(Create.vue)からオブジェクト、配列の受け渡し(CompositionAPI、ObjectはArrayも含む)
const props = defineProps({
    page: Object,
    saunas: Object,
    saunaInfos: Object,
    waterBaths: Object,
});

const { saunas, saunaInfos, waterBaths, auth} = usePage().props;

// console.log(saunas);
// console.log(saunaInfos);
// console.log(waterBaths);
// console.log(auth.user);
console.log(props);


</script>

<template>
    <AppLayout title="Sauna">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                サウナ一覧
            </h2>
        </template>

        <div>
          <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
              <div v-if="$page.props.jetstream.canUpdateProfileInformation">

                  <!-- 検索フォーム -->
                  <FormSection @submitted="updateProfileInformation" class="mb-16">
                      <template #title>
                          【検索フォーム】
                      </template>

                      <template #description>
                      </template>

                      <template #form>
                          <h3 class="col-span-6 text-xl mb-4">検索フォームの作成(GETにするべき？)</h3>
                          <p class="col-span-6 text-xl mb-4">地域(prefectures)条件(選択肢)キーワード(TextInput 半角でAND検索)サウナの温度水風呂の温度、検索ボタン</p>

                          <!-- 地域(都道府県) select -->
                          <div class="col-span-2 sm:col-span-2">
                              <SelectBox 
                                id="" 
                                label="地域"
                              />
                          </div>

                          <!-- 条件(都道府県) select -->
                          <div class="col-span-2 sm:col-span-2">
                              <SelectBox 
                                id="" 
                                label="条件詳細"  
                              />
                          </div>

                          <!-- キーワード input -->
                          <div class="col-span-2 sm:col-span-2">
                              <InputLabel for="" value="キーワード" />
                              <TextInput
                                  id=""
                                  type="text"
                                  class="mt-1 block w-full"
                                  autocomplete=""
                                  placeholder="例： 大阪 スチームサウナ "
                              />
                              <!-- <InputError :message="" class="mt-2" /> -->
                          </div>

                          <!-- サウナ(温度) select -->
                          <div class="col-span-2 sm:col-span-2">
                            <div class="grid grid-cols-6 gap-6">
                              <label class="col-span-1" for="sauna_temperature">サウナ</label>
                              <div class="col-span-2">
                                <SelectBox 
                                  id="" 
                                  label="ど"  
                                />
                              </div>
                              ~
                              <div class="col-span-2">
                                <SelectBox 
                                  id="" 
                                  label="ど"  
                                />
                              </div>
                            </div>
                          </div>

                          <!-- 水風呂(温度) select -->
                          <div class="col-span-2 sm:col-span-2">
                            <div class="grid grid-cols-6 gap-6">
                              <label class="col-span-1" for="sauna_temperature">水風呂</label>
                              <div class="col-span-2">
                                <SelectBox 
                                  id="" 
                                  label="ど"  
                                />
                              </div>
                              ~
                              <div class="col-span-2">
                                <SelectBox 
                                  id="" 
                                  label="ど"  
                                />
                              </div>
                            </div>
                          </div>

                          <!-- 検索ボタン submit→POSTかGET -->
                          <div class="col-span-2 sm:col-span-2">
                              <InputLabel for="" value="" />
                              <TextInput
                                  id=""
                                  type="text"
                                  class="mt-1 block w-full"
                                  autocomplete=""
                                  placeholder="例： 検索ボタンを配置 "
                              />
                              <!-- <InputError :message="" class="mt-2" /> -->
                          </div>

                      </template>
                    </FormSection>

                    <!-- 検索結果。一覧の表示(カードコンポーネント作成し、v-forで繰り返し処理) -->
                    <div class="mb-16 px-4 py-5 bg-white sm:p-20 shadow">
                      <h2 class="text-2xl font-bold text-gray-900 text-center mb-10 ">ここにDBから情報を引っ張ってきてカード作成</h2>
                      <p>会員登録されている方であれば、誰でも施設情報を登録・更新することができます。</p>
                      <p>皆様のサウナライフをより良いものにしていただくために、より正確な情報をご提供いただけると幸いです。</p>
                      <p>すでに施設情報が登録されており、重複登録となってしまった場合は運営側で削除させていただくこともございますので、</p>
                      <p>登録前に一度施設名で検索していただき、登録がないことを確認していただけると幸いです。</p>
                    </div>

                </div>
            </div>
        </div>
    </AppLayout>
</template>
