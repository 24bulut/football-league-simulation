import { createRouter, createWebHistory } from 'vue-router'
import HomePage from '../pages/HomePage.vue'
import LeaguePage from '../pages/LeaguePage.vue'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: HomePage
  },
  {
    path: '/league',
    name: 'League',
    component: LeaguePage
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router

