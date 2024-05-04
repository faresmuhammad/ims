import { onMounted,ref } from "vue"
import { formatTimeSince } from '@/helpers';

export function useOrders(order) {

    const items = ref([])
    const totalOrderPrice = ref(0)
    const getItems = async () => {
        const response = await axios.get('/order/' + order.reference_code + '/items')
        items.value = response.data
        totalOrderPrice.value = calculateTotalOrderPrice(items.value)
    }


    onMounted(getItems)

    
    const getProduct = (code) => axios.get('/product/' + code);

    const submitItem = async (safeToSubmit, item, extraAction = () => { }) => {
        if (!safeToSubmit) return;
        const response = await axios.post('/order/' + order.reference_code + '/items', {
            order_id: order.id,
            product_id: item.product_id,
            quantity: item.quantity,
            parts: item.parts,
            discount: item.discount,
            discount_limit: item.discount_limit,
            unit_price: item.unit_price,
            total_amount: calculateItemTotalPrice(
                item.unit_price,
                item.quantity,
                item.parts,
                item.parts_per_unit,
                item.discount
            ),
            expire_date: item.expDate
        }).catch((error) => console.log(error))

        items.value = response.data.items
        totalOrderPrice.value = calculateTotalOrderPrice(items.value)

        extraAction()
    }

    const formattedTimeSince = ref(formatTimeSince(order.created_at))

    const updateTodayTimeSince = () => {

        if (new Date(order.created_at).toDateString() === new Date().toDateString()) {
            setInterval(() => {
                formattedTimeSince.value = formatTimeSince(order.created_at);
            }, 1000 * 60)
        }
    }
    return {
        items, getItems,
        totalOrderPrice,
        getProduct,
        submitItem,
        formattedTimeSince,
        updateTodayTimeSince
    }
}

//TODO: add validation logic here





export function calculateItemTotalPrice(price, quantity, parts = 0, partsPerUnit = 1, discount = 0) {
    const discountDecimal = discount / 100;
    let totalPrice = 0;
    if (parts == 0)
        totalPrice = (price * quantity) * (1 - discountDecimal);
    else {
        const partsPerUnitChecked = partsPerUnit == null ? 1 : partsPerUnit
        totalPrice = (price * quantity + (price / partsPerUnitChecked) * parts) * (1 - discountDecimal);
    }


    return totalPrice;
}


export function calculateTotalOrderPrice(items) {
    let total = 0;
    items.forEach((item) => {
        total += item.total_amount
    })
    return total
}