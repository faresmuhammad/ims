<template>
    <Head :title="order ? 'Order #' + order.reference_code : 'No Order'"/>
    <div class="card m-3">
        <!-- Header -->
        <Toast/>
        <order-header
            :order="order"
            :suppliers="suppliers"
            title="Supplier"
            v-model:selectedSupplier="selectedSupplier"
            @update:supplier="updateSupplierOfOrder"
        />
        <!-- Table of order items -->
        <Toast position="top-center" group="tc"/>
        <DataTable
            v-model:selection="selectedItem"
            selectionMode="single"
            :value="items"
            stripedRows
            showGridlines
            editMode="cell"
            @cell-edit-complete="updateCurrentItem"
            @cell-edit-init="beginEdit"
            @keyup.ctrl.delete.exact="deleteItem"
        >
            <Column
                field="product"
                key="product.code"
                header="Code"
                class="text-center col"
            >
                <template #body="{ field, data }">
                    {{ data[field].code }}
                </template>
                <template #editor="{ field, data }">
                    <InputText v-model="data[field].code"/>
                </template>
            </Column>
            <Column
                field="product.name"
                header="Name"
                class="text-center col-3"
            ></Column>
            <Column
                field="quantity"
                header="Quantity"
                class="text-center col-1"
            >
                <template #body="{ field, data }">
                    {{ data[field] }}
                </template>
                <template #editor="{ field, data }">
                    <InputNumber v-model="data[field]" inputmode="integer"/>
                </template>
            </Column>
            <Column field="parts" header="Parts" class="text-center col-1">
                <template #body="{ field, data }">
                    {{ data[field] }}
                </template>
                <template #editor="{ field, data }">
                    <InputNumber v-model="data[field]" inputId="integer"/>
                </template>
            </Column>
            <Column
                field="unit_price"
                header="Selling Price"
                class="text-center col-1"
            >
                <template #body="{ data, field }">
                    EGP {{ data[field] }}
                </template>
                <template #editor="{ data, field }">
                    <InputNumber
                        v-model="data[field]"
                        mode="currency"
                        currency="EGP"
                    />
                </template>
            </Column>
            <Column
                field="discount"
                header="Discount"
                class="text-center col-1"
            >
                <template #body="{ field, data }">
                    %{{ data[field] }}
                </template>
                <template #editor="{ field, data }">
                    <InputNumber
                        v-model="data[field]"
                        inputId="percent"
                        prefix="%"
                        :min="0"
                        :max="100"
                        @input="validate($event.value, 'discount','current')"
                    />
                </template>
            </Column>
            <Column
                field="discount_limit"
                header="Discount Limit"
                class="text-center col-1"
            >
                <template #body="{ field, data }">
                    %{{ data[field] }}
                    <small class="p-error">{{ errorsCurrent.discountLimit }}</small>
                </template>
                <template #editor="{ field, data }">
                    <div class="flex flex-column">
                        <InputNumber
                            v-model="data[field]"
                            inputId="percent"
                            prefix="%"
                            :min="0"
                            :max="100"
                            @input="validate($event.value,'discount_limit','current')"
                        />
                        <small class="p-error">{{ errorsCurrent.discountLimit }}</small>
                    </div>
                </template>
            </Column>
            <Column
                field="expire_date"
                header="Exp. Date"
                class="text-center col-2"
            >
                <template #body="{ field, data }">
                    {{
                        data[field]
                            ? formatExpireDate(data[field])
                            : "Empty Exp. Date"
                    }}
                </template>
                <template #editor="{ field, data }">
                    <div class="flex flex-column">
                        <InputMask
                            id="basic"
                            v-model="data[field]"
                            placeholder="02/2025"
                            mask="99/9999"
                            slotChar="mm/yyyy"
                            @update:modelValue="validate($event, 'expDate','current')"
                        />
                        <small
                            v-if="errorsCurrent.expireDate"
                            :class="errorsCurrent.expireDate.severity === 'error' ? 'p-error': 'text-yellow-600'">{{
                                errorsCurrent.expireDate.message
                            }}</small>
                    </div>
                </template>
            </Column>
            <Column
                field="total_amount"
                header="Total Price"
                class="text-center col-1"
            >
                <template #body="{ data, field }">
                    EGP {{ data[field].toFixed(2) }}
                </template>
            </Column>

            <template #footer>
                <div
                    @keydown.ctrl.enter="submitNewItem"
                    @keyup.enter.exact="getProductData"
                    class="card p-3"
                    style="border-color: var(--primary-color)"
                >
                    <div class="formgrid grid">
                        <!-- feedback: product not found -->
                        <div class="field col-2">
                            <div class="flex flex-column gap-2">
                                <label for="code">Code</label>
                                <InputText
                                    v-model="newItem.code"
                                    class="w-full"
                                    id="code"
                                    :invalid="errorsNew.code !== null"
                                />
                                <small class="p-error">{{ errorsNew.code }}</small>
                            </div>
                        </div>
                        <div class="field col-3">
                            <div class="flex flex-column gap-2">
                                <label for="name">Name</label>
                                <InputText
                                    v-model="newItem.name"
                                    class="w-full"
                                    id="name"
                                    readonly
                                />
                            </div>
                        </div>
                        <div class="field col-1 m-0">
                            <div class="flex flex-column gap-2">
                                <label for="quantity">Quantity</label>
                                <InputNumber
                                    v-model="newItem.quantity"
                                    inputClass="w-full"
                                    id="quantity"
                                    inputId="integer"
                                />
                            </div>
                        </div>
                        <div class="field col-1">
                            <div class="flex flex-column gap-2">
                                <label for="parts">Parts</label>
                                <!-- TODO: track user input, if it exceeded the parts per unit show a warning popup with action to add the number of parts per unit to quantity and set to parts the remaining -->
                                <InputNumber
                                    v-model="newItem.parts"
                                    inputClass="w-full"
                                    id="parts"
                                    inputId="integer"
                                />
                            </div>
                        </div>
                        <div class="field col-1">
                            <div class="flex flex-column gap-2">
                                <label for="selling-price">Selling Price</label>
                                <InputNumber
                                    v-model="newItem.unit_price"
                                    inputClass="w-full"
                                    id="selling-price"
                                    mode="currency"
                                    currency="EGP"
                                />
                            </div>
                        </div>
                        <div class="field col-1">
                            <div class="flex flex-column gap-2">
                                <label for="discount">Discount</label>
                                <InputNumber
                                    v-model="newItem.discount"
                                    inputClass="w-full"
                                    id="discount"
                                    inputId="percent"
                                    prefix="%"
                                    :min="0"
                                    :max="100"
                                    @input="validate($event.value, 'discount','new')"
                                />
                            </div>
                        </div>
                        <div class="field col-1">
                            <div class="flex flex-column gap-2">
                                <label for="discount_limit"
                                >Discount Limit</label
                                >
                                <InputNumber
                                    v-model="newItem.discount_limit"
                                    inputClass="w-full"
                                    id="discount_limit"
                                    inputId="percent"
                                    prefix="%"
                                    :min="0"
                                    :max="100"
                                    @input="validate($event.value,'discount_limit','new')"
                                    :invalid="errorsNew.discountLimit !== null"
                                />
                                <small class="p-error">{{
                                        errorsNew.discountLimit
                                    }}</small>
                            </div>
                        </div>
                        <div class="field col-1">
                            <div class="flex flex-column gap-2">
                                <label for="exp-date">Exp. Date</label>
                                <InputMask
                                    id="exp-date"
                                    v-model="newItem.expDate"
                                    placeholder="02/2025"
                                    mask="99/9999"
                                    slotChar="mm/yyyy"
                                    inputClass="w-full"
                                    @update:modelValue="validate($event, 'expDate','new')"
                                />
                                <small
                                    v-if="errorsNew.expireDate"
                                    :class="
                                        errorsNew.expireDate.severity ===
                                        'error'
                                            ? 'p-error'
                                            : 'text-yellow-600'
                                    "
                                >{{ errorsNew.expireDate.message }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </DataTable>
        <!-- Total Price -->
        <h5>Total Price: {{ totalOrderPrice }}</h5>
        <!-- Save and cancel buttons -->
        <div :class="order.completed ? 'hidden' : 'flex justify-content-end'">
            <Button label="Cancel" outlined severity="danger"/>
            <Button
                label="Save"
                class="mx-2"
                severity="success"
                @click="completeOrder"
            />
        </div>
    </div>
</template>

<script setup>
import {Head, router} from "@inertiajs/vue3";
import {onMounted, reactive, ref} from "vue";
import axios from "axios";
import {useToast} from "primevue/usetoast";
import Toast from "primevue/toast";
import {useOrders, calculateItemTotalPrice} from "@/composables/orders";
import {formatDateTime, formatExpireDate} from "@/helpers";
import OrderHeader from "@/Components/OrderHeader.vue";
import moment from "moment";
import {supplierValidator} from "../../composables/orders";

const props = defineProps({
    order: Object,
    suppliers: Object,
});

const {
    items,
    getItems,
    totalOrderPrice,
    getNewProduct,
    submitItem,
    updateItem,
    resetForm,
} = useOrders(props.order);
const toast = useToast();

const selectedSupplier = ref(props.order.supplier_id);

const updateSupplierOfOrder = (event) => {
    router.put(
        "/orders/" + props.order.id,
        {
            supplier_id: selectedSupplier.value,
        },
        {
            onSuccess: (page) => {
                console.log(page);
            },
        }
    );
};

const selectedItem = ref();
const newItem = reactive({
    product_id: null,
    code: "",
    name: "",
    quantity: 1,
    parts: 0,
    discount: 0,
    discount_limit: 0,
    unit_price: 0,
    parts_per_unit: null,
    expDate: "",
});

const current = reactive({
    id: null,
    product_id: null,
    code: "",
    name: "",
    quantity: 1,
    parts: 0,
    discount: 0,
    discount_limit: 0,
    unit_price: 0,
    parts_per_unit: null,
    totalPrice: 0,
    expDate: "",
});


const {errorsNew, errorsCurrent, validate, validateProduct} = supplierValidator(newItem, current)


const getProductData = () => {
    errorsNew.code = null
    getNewProduct(newItem, 'supplier', (message) => {
        errorsNew.code = message
    })
}
const submitNewItem = () => {
    const safeToSubmit =
        !(errorsNew.code ||
            errorsNew.discountLimit ||
            errorsNew.expireDate.message);
    submitItem(safeToSubmit, newItem);
};

const updateCurrentItem = (event) => {
    const safeToUpdate =
        !(errorsCurrent.code ||
            errorsCurrent.discountLimit ||
            errorsCurrent.expireDate.message);
    if (!safeToUpdate) {
        Object(errorsCurrent).values().forEach((e) => {
            console.log('errors', e)
        })
    }
    updateItem(event, current, safeToUpdate, 'supplier', (page) => {
        toast.add({
            severity: page.props.flash.severity,
            summary: page.props.flash.message,
            life: 2000,
        });
    }, (message) => {
        errorsCurrent.code = message
        toast.add({severity: "error", summary: message, life: 2000, group: 'tc'})
    });
};

const beginEdit = (event) => {
    errorsCurrent.code = null;
    errorsCurrent.discountLimit = null;
    errorsCurrent.expireDate.message = null;
    const item = event.data;
    current.id = item.id;
    current.product_id = item.product.id;
    current.code = item.product.code;
    current.name = item.product.name;
    current.quantity = item.quantity;
    current.parts = item.parts;
    current.discount = item.discount;
    current.discount_limit = item.discount_limit;
    current.unit_price = item.unit_price;
    current.parts_per_unit = item.parts_per_unit;
    current.totalPrice = item.total_amount;
    current.expDate = item.expire_date;
};

const deleteItem = () => {
    console.log(selectedItem.value);
    if (selectedItem.value) {
        router.delete("/order/item/" + selectedItem.value.id, {
            onSuccess: () => {
                console.log("item deleted");
                getItems();
            },
        });
    } else {
        toast.add({
            severity: "error",
            summary: "Please, select an item to delete",
        });
    }
};

const completeOrder = () => {
    //loop over the items
    const stocks = [];
    items.value.forEach((item) => {
        //prepare stock object
        const stock = {
            product_id: item.product.id,
            supplier_id: selectedSupplier.value,
            quantity: item.quantity,
            parts: item.parts,
            price: item.unit_price,
            discount: item.discount,
            discount_limit: item.discount,
            expire_date: item.expire_date,
        };

        stocks.push(stock);
    });
    //TODO:: handle validation flash errors
    axios
        .post("/order/supplier/complete", {
            stocks: stocks,
            order_id: props.order.id,
        })
        .then((res) => {
            console.log("stocks added", res.data);
        })
        .catch((error) => {
            // console.log(error);
        });
    //send create stock requests
    //update order to completed
};
</script>
