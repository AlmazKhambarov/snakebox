<template>
  <div class="main__wrapper__Lh0So">
    <div class="content__container__Renq7">
      <div class="title__BiahX">404</div>
      <div class="sub_title__2Z0eP">
        <div
          class="text__OlAkW color--inherit__nPrtI variant--h2__DCK61 align--center__VQDbt bold__nhebz uppercase__iz9x_">
          Страница не найдена!</div>
      </div>
    </div>
  </div>
</template>

<script>
import { useSeo } from "@/composables/useSeo.js";

export default {
  name: "NotFound",
  setup() {
    // Инициализируем SEO для страницы 404
    const { seoData, updateOpenGraph } = useSeo("404");

    return {
      seoData,
      updateOpenGraph
    };
  },
};
</script>

<style>
.layout__Akb41 .main__wrapper__Lh0So {
    display: flex;
    flex-direction: column;
    flex-grow: 3;
}

.content__container__Renq7 {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    flex-grow: 3;
    min-height: 500px;
}

.content__container__Renq7 .title__BiahX {
    text-align: center;
    font-size: 89px;
    font-style: normal;
    font-weight: 900;
    text-transform: uppercase;
    background: linear-gradient(180deg, #345045, rgba(24, 25, 36, 0) 142.7%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent; 
}

@media (min-width: 768px) {
    .content__container__Renq7 .title__BiahX {
        font-size: 179px;
        line-height: 140px;
    }
}

.content__container__Renq7 .sub_title__2Z0eP {
    color: var(--primary);
    margin-top: 10px;
}

.text__OlAkW {
    font-weight: 500;
    padding: 0;
    margin: 0;
}

.text__OlAkW.uppercase__iz9x_ {
    text-transform: uppercase;
}

.text__OlAkW.align--center__VQDbt {
    text-align: center;
}

.text__OlAkW.color--inherit__nPrtI {
    color: inherit;
}

.text__OlAkW.variant--h2__DCK61 {
    font-size: 1.5rem;
    line-height: 1.625rem;
    font-weight: 900;
}

.text__OlAkW.variant--h2__DCK61.bold__nhebz {
    font-weight: 900;
}
</style>