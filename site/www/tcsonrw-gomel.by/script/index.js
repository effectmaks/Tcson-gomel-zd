let burger = document.querySelector('.burger')
let menu = document.querySelector('.header-nav')
let menuLinks = document.querySelectorAll('.header-item')

// Добавляем слушатель событий на всем документе
document.addEventListener('click', function (event) {
  // Проверяем, был ли клик выполнен вне области меню
  if (event.target.closest('.header-down-container') === null) {
    if (menu.classList.contains('header-nav-active')) { // это родитель кнопки
      // Если это так, мы удаляем классы active из кнопки меню и списка меню, чтобы закрыть его
      burger.classList.remove('burger__active');
      menu.classList.remove('header-nav-active');
    }
  }
});


// Получаем все элементы с классом "service"
const serviceItems = document.querySelectorAll('.service');

// Перебираем каждый элемент "service" и добавляем обработчик события щелчка
serviceItems.forEach(function(item) {
  item.addEventListener('click', function() {
// Проверяем, отсутствует ли у элемента класс "no_open"
    if (!this.classList.contains('no_open')) {
      // Удаляем класс "service__active" у всех элементов "service"
      serviceItems.forEach(function(item) {
        item.classList.remove('service__active');
      });

      // Добавляем класс "service__active" к элементу, по которому произошло событие щелчка
      this.classList.add('service__active');
    }
  });
});

burger.addEventListener('click', function () {
  console.log(2);
  burger.classList.toggle('burger__active');
  menu.classList.toggle('header-nav-active');
});

menuLinks.forEach(function (item) {
  item.addEventListener('click', function () {
    burger.classList.remove('burger__active');
    menu.classList.remove('header-nav-active');
  });
});

$(document).ready(function () {
  $('.mainnews__slider').slick({
    slidesToShow: 3,
    slidesToScroll: 3,
    autoplay: true,
    autoplaySpeed: 14000,
    infinite: true,
    arrows: false,
    dots: true,
    dotsClass: 'mainnews__dots',
    responsive: [
      {
        breakpoint: 768,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 2
        }
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1
        }
      }
    ]
  });
});

$(document).ready(function () {
  $('.mainadv__slider').slick({
    slidesToShow: 3,
    slidesToScroll: 3,
    autoplay: true,
    autoplaySpeed: 8000,
    infinite: true,
    arrows: false,
    dots: true,
    dotsClass: 'mainadv__dots',
    responsive: [
      {
        breakpoint: 768,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 2
        }
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1
        }
      }
    ]
  });
});


