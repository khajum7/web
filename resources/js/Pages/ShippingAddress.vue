<script setup>
import axios from "axios";
import AppButton from "@/Components/AppButton.vue";
import PublicLayout from "@/Layouts/PublicLayout.vue";
import { router } from '@inertiajs/vue3';
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import AppDatepicker from "@/Components/AppDatePicker.vue";
import { ref, onMounted, nextTick, computed } from 'vue';
import { mask as vMask } from 'vue-the-mask';
import { useForm } from 'vee-validate'
import * as Yup from 'yup';
import { useSessionStorage } from "@vueuse/core"
import { Head } from "@inertiajs/vue3";
import { useGoogleMapApi } from '@/Composables/useGoogleMapApi'
const selectedJerseys = useSessionStorage("selectedJerseys", {})
const selectedItems = useSessionStorage("selectedItems", {})
const isLoading = ref(false)
const fullAddress = ref()
selectedItems.value.shipping_address = {
    delivery_date: '',
    date_first_game: '',
    organization_name: '',
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    address1: '',
    city: '',
    state: '',
    zip: '',
    country: 'US',
    email_confirmation: '',
};
const apiKey = import.meta.env.VITE_GOOGLE_KEY //It'll get key from env file base on mode.
onMounted( async () => {
    await useGoogleMapApi(apiKey)
    googleAddress()
  if(sessionStorage.getItem('state') == undefined || sessionStorage.getItem('state') == null || sessionStorage.getItem('state') == 0) {
    router.get(route('public.index'));
  }else if(sessionStorage.getItem('state') == 1){
    router.get(route('public.jersey-set', { set: selectedJerseys.value.set_8.selected ? 8 : 10 }));
  }
  if (selectedJerseys.value.shipping_address) {
        selectedJerseys.value.shipping_address = selectedJerseys.value.shipping_address; 
    }
    await nextTick(() => {
        if (selectedJerseys.value.shipping_address !== undefined && Object.keys(selectedJerseys.value.shipping_address).length !== 0 ) {
            setValues(selectedJerseys.value.shipping_address);
        }
    })
});
const googleAddress = () => {
  let autocomplete;
  let address1

  address1 = document.getElementById('address1')

  const options = {
    fields: ['address_components', 'geometry'],
    componentRestrictions: {
      country: 'us'
    },
    strictBounds: false,
    types: ['address']
  }

  autocomplete = new google.maps.places.Autocomplete(address1, options)

  autocomplete.addListener('place_changed', () => {
    const place = autocomplete.getPlace()
    console.log(place)

    if (!place.geometry || !place.address_components) {
      return
    }

    let address1Field = ''
    let address2Field = ''
    let zipField = ''
    let cityField = ''
    let stateField = ''

    for (const component of place.address_components) {
      const componentType = component.types[0]

      switch (componentType) {
        case 'street_number': {
          address1Field = `${component.long_name} ${address1Field}`
          break
        }
        case 'subpremise': {
          address2Field = `${component.long_name} ${address2Field}`
          break
        }

        case 'route': {
          address1Field += component.long_name
          break
        }

        case 'postal_code': {
          zipField = `${component.long_name}${zipField}`
          break
        }

        case 'locality': {
          cityField = component.long_name
          break
        }

        case 'administrative_area_level_1': {
          stateField = component.short_name
          break
        }
      }
    }
    fullAddress.value = {
      address1: address1Field,
      address2: address2Field,
      city: cityField,
      state: stateField,
      zip: zipField
    }
    updateAddress()
  })
}
const updateAddress = () => {
  setFieldValue('address1', fullAddress.value.address1)
  setFieldValue('city', fullAddress.value.city)
  setFieldValue('state', fullAddress.value.state)
  setFieldValue('zip', fullAddress.value.zip)
  setFieldValue('address2', fullAddress.value.address2)
}
// Validation schema
const schema = Yup.object().shape({
    delivery_date: Yup.string().required('Ideal Delivery date is required'),
    date_first_game: Yup.string().required('Date first game is required'),
    organization_name: Yup.string().required('Organization name is required'),
    first_name: Yup.string().required('First name is required'),
    last_name: Yup.string().required('Last name is required'),
    email: Yup.string().email().required('Email is required').label('Email'),
    phone: Yup.string().required('Phone number is required'),
    address1: Yup.string().required('Address line 1 is required'),
    city: Yup.string().required('City is required'),
    state: Yup.string().required('State is required'),
    zip: Yup.string().required('Zip is required').matches(/^\d+$/, 'Zip must be a number'),
    email_confirmation: Yup.string()
      .oneOf([Yup.ref('email'), null], 'Email must match')
      .required('Confirm email is required'),
});

const { handleSubmit, errors, defineField, setValues, setFieldValue } = useForm({
  validationSchema: schema,
  initialValues: selectedItems.value.shipping_address
});

const [delivery_date,deliveryDateAttrs] = defineField('delivery_date')
const [date_first_game,dateFirstGameAttrs] = defineField('date_first_game')
const [organization_name,organizationNameAttrs] = defineField('organization_name')
const [first_name, firstNameAttrs] = defineField('first_name');
const [last_name, lastNameAttrs] = defineField('last_name');
const [address1, address1Attrs] = defineField('address1');
const [address2, address2Attr] = defineField('address2');
const [city, cityAttrs] = defineField('city');
const [state, stateAttrs] = defineField('state');
const [zip, zipAttrs] = defineField('zip');
const [phone, phoneAttrs] = defineField('phone');
const [email, emailAttrs] = defineField('email');
const [email_confirmation, email_confirmation_attrs] = defineField('email_confirmation');
const payloadItems = computed(() => {
    const sections = [
        selectedJerseys.value?.set_8?.items,
        selectedJerseys.value?.set_10?.items,
        selectedJerseys.value?.extra_jerseys_shorts[0]?.items,
        selectedJerseys.value?.extra_jerseys_shorts[1]?.items
    ];

    return sections.flatMap(section => 
        section?.filter(item => item.qty > 0).map(item => {
            return {
                qty: item.qty,
                sku: item.sku_number
            }
        }) || []
    ).filter((obj) => obj.sku !== undefined);
});

const onSubmit = handleSubmit( async(values) => {
    isLoading.value = true;
    sessionStorage.setItem('state', 3);
    const data = {
        ...values,
        country: 'US',
    };
    const payload = payloadItems.value.map((obj) => obj.sku)
    selectedJerseys.value.shipping_address = data
    try {
        await axios.post("api/sku/details", {sku: payload}).then((res) => {
            if(res.status == 200){
                // selectedItems.value = res.data.data;
                const group_names = ["10 pk Jersey Set", "8 pk Jersey Set", "Single Reversible Jersey", "Single Reversible Short"];
                selectedItems.value = payloadItems.value.map((obj) => {
                    const itemName = res.data.data.find((res) => res.sku == obj.sku)
                    return {
                        group_name: group_names[itemName.type - 1],
                        name: itemName.name,
                        sku: obj.sku,
                        type: itemName.type,
                        qty: obj.qty,
                        total: itemName.type == '1' ? (obj.qty * 10) : itemName.type == '2' ? (obj.qty * 8) : obj.qty,
                    }
                })
                router.get(route('public.order-preview'));
                isLoading.value = false;
            }
        })
    } catch (err) {
        console.log("Error", err);
        isLoading.value = false;
    }
});

const sendBack = () => {
    window.history.back();
}
</script>

<template>
    <Head title="Shipping Address" />
    <PublicLayout>
        <div class="flex flex-col justify-center gap-6 py-8 xl:py-5">
            <h2 class="text-3xl font-normal font-heading">Shipping Address</h2>
            <form @submit.prevent="onSubmit" class="flex flex-col gap-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <AppDatepicker name="delivery_date" label="Ideal Delivery Date" placeholder="MM/DD/YYYY" v-model="delivery_date" v-bind="deliveryDateAttrs" />
                    </div>
                    <div>
                        <AppDatepicker name="date_first_game" label="Date of First Game" placeholder="MM/DD/YYYY" v-model="date_first_game" v-bind="dateFirstGameAttrs" />
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
                    <div>
                        <InputLabel for="organization_name" value="Organization Name" />
                        <TextInput id="organization_name" type="text" name="organization_name" class="block w-full mt-1" v-model="organization_name" v-bind="organizationNameAttrs" />
                        <InputError v-if="errors.organization_name" class="mt-2" :message="errors.organization_name" />
                    </div>
                    <div>
                        <InputLabel for="first_name" value="Ship To First Name" />
                        <TextInput id="first_name" type="text" class="block w-full mt-1" name="first_name" v-model="first_name" v-bind="firstNameAttrs" />
                        <InputError v-if="errors.first_name" class="mt-2" :message="errors.first_name" />
                    </div>
                    <div>
                        <InputLabel for="last_name" value="Ship To Last Name" />
                        <TextInput id="last_name" type="text" name="last_name" class="block w-full mt-1" v-model="last_name" v-bind="lastNameAttrs" />
                        <InputError v-if="errors.last_name" class="mt-2" :message="errors.last_name" />
                    </div>
                </div>
                <div>
                    <InputLabel for="address1" value="Shipping Address Line 1" />
                    <TextInput id="address1" type="text" name="address1" class="block w-full mt-1" v-model="address1" v-bind="address1Attrs" />
                    <InputError v-if="errors.address1" class="mt-2" :message="errors.address1" />
                </div>
                <div>
                    <InputLabel for="address2" value="Shipping Address Line 2" />
                    <TextInput id="address2" type="text" name="address2" class="block w-full mt-1" v-model="address2" v-bind="address2Attr" />
                    <InputError v-if="errors.address2" class="mt-2" :message="errors.address2" />
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <InputLabel for="city" value="City" />
                        <TextInput id="city" type="text" name="city" class="block w-full mt-1" v-model="city" v-bind="cityAttrs" />
                        <InputError v-if="errors.city" class="mt-2" :message="errors.city" />
                    </div>
                    <div>
                        <InputLabel for="state" value="State" />
                        <TextInput id="state" type="text" name="state" class="block w-full mt-1" v-model="state" v-bind="stateAttrs" />
                        <InputError v-if="errors.state" class="mt-2" :message="errors.state" />
                    </div>
                    <div>
                        <InputLabel for="zip" value="Zip" />
                        <TextInput id="zip" type="text" name="zip" class="block w-full mt-1" v-model="zip" v-bind="zipAttrs" />
                        <InputError v-if="errors.zip" class="mt-2" :message="errors.zip" />
                    </div>
                </div>
                <div>
                    <InputLabel for="phone" value="Phone Number" />
                    <TextInput id="phone" type="text" name="phone" class="block w-full mt-1" v-model="phone" v-bind="phoneAttrs" v-mask="'(###)-###-####'" />
                    <InputError v-if="errors.phone" class="mt-2" :message="errors.phone" />
                </div>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <InputLabel for="email" value="Email" />
                        <TextInput id="email" type="email" name="email" class="block w-full mt-1" v-model="email" v-bind="emailAttrs" />
                        <InputError v-if="errors.email" class="mt-2" :message="errors.email" />
                    </div>
                    <div>
                        <InputLabel for="email_confirmation" value="Confirm Email" />
                        <TextInput id="email_confirmation" type="email" name="email_confirmation" class="block w-full mt-1" v-model="email_confirmation" v-bind="email_confirmation_attrs" />
                        <InputError v-if="errors.email_confirmation" class="mt-2" :message="errors.email_confirmation" />
                    </div>
                </div>
                <div class="grid justify-between grid-cols-2 gap-2 pt-2 font-heading">
                    <AppButton variant="secondary" @click="sendBack" class="px-2 text-2xl py-7 font-heading">
                        Back
                    </AppButton>
                    <AppButton type="submit" class="px-2 text-2xl py-7 font-heading">
                        Continue
                    </AppButton>
                </div>
            </form>
        </div>
    </PublicLayout>
</template>

<style>
.app_datepicker .dp__input {
  --dp-font-size: 16px;
  --dp-border-color: #d1d5db;
  @apply shadow-sm
}
.app_datepicker{
    --dp-font-family : var(--dp-font-family)
}
.pac-container:after {
  display: none !important;
}

</style>
