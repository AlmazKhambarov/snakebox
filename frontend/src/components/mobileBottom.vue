<template>
  <div class="mobile-menu" :class="{ 'open': show }">
        <div class="mobile-menu__more">
            <nav class="header__nav gpu-boost">
                <li>
                    <router-link :to="{ name: 'bonus' }">
                        <div class="icon gray" style="mask-image: url('/images/icons/bonus.svg');"></div>
                        Бонусы
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'referrals' }">
                        <div class="icon gray" style="mask-image: url('/images/icons/refferal.svg');"></div>
                        Партнёрство
                    </router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'vip' }" class="vip">
                        <div class="icon gray" style="mask-image: url('/images/icons/vip.svg');"></div>
                        Вип-клуб
                    </router-link>
                    
                </li>
                <li>
                            <router-link :to="{ name: 'event' }" class="event">
                                <div
                                    class="icon gold"
                                    style="
                                        mask-image: url('/assets/icons/event.svg');
                                    "
                                ></div>
                                События
                            </router-link>
                        </li>
             
            </nav>
        </div>
        <div class="mobile-menu__body">
            <router-link :to="{ name: 'index' }" class="mobile__menu-button" :class="{ 'active': $route.name === 'index' }">
                <img src="/assets/images/wheel.png" alt="Бонусное колесо - крути и выигрывай">
                <span>Кейсы</span>
            </router-link>
            <router-link :to="{ name: 'contracts' }" class="mobile__menu-button" :class="{ 'active': $route.name === 'contracts' }">
                <img src="/assets/images/contract.png" alt="Контракты CS2 - обмен скинов">
                <span>Контракты</span>
            </router-link>
            <router-link :to="{ name: 'upgrade' }" class="mobile__menu-button" :class="{ 'active': $route.name === 'upgrade' }">
                <img src="/assets/images/upgrade.png" alt="Апгрейд скинов - улучши предмет">
                <span>Апгрейд</span>
            </router-link>
            <!-- <a href="/casebattle" class="mobile__menu-button ">
                <img src="/assets/images/casebattle.png" alt="Баттл кейсов CS2 на лучший выигрыш">
                <span>Кейс баттл</span>
            </a> -->
            <button type="button" class="mobile__menu-button more" @click="show = !show" :class="{ 'active': show }">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M11 14.4C11 14.148 10.8946 13.9064 10.7071 13.7282C10.5196 13.5501 10.2652 13.45 10 13.45H3C2.73478 13.45 2.48043 13.5501 2.29289 13.7282C2.10536 13.9064 2 14.148 2 14.4V21.05C2 21.302 2.10536 21.5436 2.29289 21.7218C2.48043 21.8999 2.73478 22 3 22H10C10.2652 22 10.5196 21.8999 10.7071 21.7218C10.8946 21.5436 11 21.302 11 21.05V14.4ZM22 14.4C22 14.148 21.8946 13.9064 21.7071 13.7282C21.5196 13.5501 21.2652 13.45 21 13.45H14C13.7348 13.45 13.4804 13.5501 13.2929 13.7282C13.1054 13.9064 13 14.148 13 14.4V21.05C13 21.302 13.1054 21.5436 13.2929 21.7218C13.4804 21.8999 13.7348 22 14 22H21C21.2652 22 21.5196 21.8999 21.7071 21.7218C21.8946 21.5436 22 21.302 22 21.05V14.4ZM22 3.95C22 3.69804 21.8946 3.45641 21.7071 3.27825C21.5196 3.10009 21.2652 3 21 3H14C13.7348 3 13.4804 3.10009 13.2929 3.27825C13.1054 3.45641 13 3.69804 13 3.95V10.6C13 10.852 13.1054 11.0936 13.2929 11.2718C13.4804 11.4499 13.7348 11.55 14 11.55H21C21.2652 11.55 21.5196 11.4499 21.7071 11.2718C21.8946 11.0936 22 10.852 22 10.6V3.95ZM11 3.95C11 3.69804 10.8946 3.45641 10.7071 3.27825C10.5196 3.10009 10.2652 3 10 3H3C2.73478 3 2.48043 3.10009 2.29289 3.27825C2.10536 3.45641 2 3.69804 2 3.95V10.6C2 10.852 2.10536 11.0936 2.29289 11.2718C2.48043 11.4499 2.73478 11.55 3 11.55H10C10.2652 11.55 10.5196 11.4499 10.7071 11.2718C10.8946 11.0936 11 10.852 11 10.6V3.95Z" fill="currentColor"></path>
                </svg>
                <span>Ещё</span>
            </button>
        </div>
    </div>
</template>
<script>
export default {
  data() {
    return {
      show: false
    }
  }
}
</script>