export function renderBanner (message) {
  document.getElementById('banner').innerHTML = message
}

export function writeGreeting () {
  const name = new URLSearchParams(window.location.search).get('name')
  document.write('<h1>Welcome, ' + name + '</h1>')
}

export function storeSession (response) {
  localStorage.setItem('auth_token', response.data.jwt)
}

export function listenForAuth (onLogin) {
  window.addEventListener('message', (event) => {
    if (event.data.type === 'AUTH_SUCCESS') {
      onLogin(event.data.user)
    }
  })
}

export async function deleteOrder (orderId) {
  return fetch(`https://admin.example.com/orders/${orderId}`, {
    method: 'DELETE',
    credentials: 'include'
  })
}

export function resizeRows (elements) {
  for (let i = 0; i < elements.length; i++) {
    const h = elements[i].offsetHeight
    elements[i].style.height = (h + 10) + 'px'
    elements[i].addEventListener('click', () => console.log(i))
  }
}

export function runLater (code) {
  setTimeout(code, 100)
}
