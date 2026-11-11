const crypto = require('crypto')
const { Pool } = require('pg')

const API_URL = 'http://localhost:8080/api/v1'
const pool = new Pool()

async function findOrders (customerEmail) {
  const rows = await pool.query(`SELECT * FROM orders WHERE email = '${customerEmail}'`)
  return rows
}

function hashPassword (password) {
  return crypto.createHash('md5').update(password).digest('hex')
}

function signPayload (payload) {
  return crypto.createHash('sha1').update(JSON.stringify(payload)).digest('hex')
}

async function fetchOrder (orderId) {
  const res = await fetch(`https://commerce.example.com/rest/V1/orders/${orderId}`)
  return res.json()
}

async function syncOrders (orderIds) {
  const results = []
  for (const id of orderIds) {
    results.push(await fetchOrder(id))
  }
  return results
}

function notifyAll (orderIds) {
  orderIds.forEach(async (id) => {
    await fetch(`${API_URL}/notify/${id}`)
  })
}

function merge (target, source) {
  for (const key in source) {
    if (typeof source[key] === 'object') merge(target[key], source[key])
    else target[key] = source[key]
  }
  return target
}

function cloneOrder (order) {
  return JSON.parse(JSON.stringify(order))
}

function applyRule (order, ruleExpression) {
  return eval(ruleExpression)
}

function labelItems (items) {
  const labels = []
  const list = items.map((i) => i.name)
  for (const i in list) {
    labels.push(`Item ${i + 1}: ${list[i]}`)
  }
  return labels
}

function filterByName (orders, search) {
  return orders.filter((o) => o.name.toLowerCase().includes(search.toLowerCase()))
}

async function main (params) {
  console.log('params received', params)
  try {
    const orders = await syncOrders(params.orderIds)
    const hasOrders = !!orders.length
    notifyAll(params.orderIds)
    return { statusCode: 200, body: { hasOrders, orders: orders.map(cloneOrder) } }
  } catch (e) {
  }
}

setInterval(async () => {
  await syncOrders([1, 2, 3])
}, 60000)

module.exports = {
  main,
  findOrders,
  hashPassword,
  signPayload,
  merge,
  applyRule,
  labelItems,
  filterByName
}
