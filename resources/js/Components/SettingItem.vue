<template>
    <li class="flex align-items-center py-3 px-2  surface-overlay w-full">
        <div class="text-500 w-full md:w-2 font-medium">{{ setting.key }}</div>
        <div class="text-500 w-full md:w-2 font-medium">

            <div v-if="setting.value_type === 'date' || setting.value_type === 'date_range'"
                 class="flex align-items-center justify-content-between w-full gap-3">
                <Calendar v-model="value" :placeholder="datePlaceholder"
                          date-format="dd/mm/yy" :disabled="!edit" :selection-mode="range ? 'range' : 'single'"/>
                <div class="flex align-items-center gap-1">
                    <Checkbox v-model="range" :binary="true" :disabled="!edit"/>
                    Range
                </div>
            </div>
            <InputNumber v-else v-model="value" :disabled="!edit" :placeholder="setting.value_integer"/>
        </div>
    </li>
</template>


<script setup>
import {ref} from "vue";
import {formatDateTime} from "../helpers";

const props = defineProps({
    setting: Object,
    edit: {
        type: Boolean,
        default: false,
    }
})
const value = defineModel("value");
const format = (date) => {
    if (date !== '')
        return formatDateTime(date, 'DD/MM/YYYY');
}
const range = ref(props.setting.value_start_date !== null);
const datePlaceholder = props.setting.value_date ? format(props.setting.value_date) : format(props.setting.value_start_date) + ' - ' + format(props.setting.value_end_date);
</script>


