/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../css/app.scss')

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
// const $ = require('jquery');

document.addEventListener('DOMContentLoaded', () => {
  const burger = document.querySelector('#burger')
  const navbarMenu = document.querySelector('#navbarMenu')

  burger.addEventListener('click', () => {
    burger.classList.toggle('is-active')
    navbarMenu.classList.toggle('is-active')
  })
})

document.addEventListener('DOMContentLoaded', () => {
  const hasDropDown = document.querySelectorAll('.has-dropdown')

  hasDropDown.forEach((el) => {
    el.addEventListener('click', () => {
      el.classList.toggle('is-active')
    })
  })
})
