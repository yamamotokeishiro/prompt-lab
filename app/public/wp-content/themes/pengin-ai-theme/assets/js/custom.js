/**
 * PENGIN AI Theme - Main JavaScript
 */
(function ($) {
  "use strict";

  // DOM読み込み完了時の処理
  $(document).ready(function () {
    initMobileMenu();
    initScrollIndicator();
    generateParticles();
    initCourseCardEffects();
    animateProgressBar();
    initScrollAnimations();
  });

  /**
   * モバイルメニューの初期化
   */
  function initMobileMenu() {
    // jQueryバージョンのモバイルメニュートグル（互換性のため残す）
    $(".mobile-menu-toggle").on("click", function () {
      $(this).toggleClass("active");
      $(".main-navigation").toggleClass("active");
      $("body").toggleClass("menu-open");
    });

    // メニュー外クリックで閉じる
    $(document).on("click", function (event) {
      var $mainNav = $(".main-navigation");
      var $menuToggle = $(".mobile-menu-toggle");

      if (
        $mainNav.hasClass("active") &&
        !$mainNav.is(event.target) &&
        $mainNav.has(event.target).length === 0 &&
        !$menuToggle.is(event.target) &&
        $menuToggle.has(event.target).length === 0
      ) {
        $menuToggle.removeClass("active");
        $mainNav.removeClass("active");
        $("body").removeClass("menu-open");
      }
    });

    // リサイズ時のメニュー状態調整
    $(window).on("resize", function () {
      if ($(window).width() > 768 && $(".main-navigation").hasClass("active")) {
        $(".mobile-menu-toggle").removeClass("active");
        $(".main-navigation").removeClass("active");
        $("body").removeClass("menu-open");
      }
    });
  }

  /**
   * スクロールインジケーターの初期化
   */
  function initScrollIndicator() {
    $(".scroll-indicator").on("click", function () {
      $("html, body").animate(
        {
          scrollTop: $("#courses").offset().top,
        },
        800
      );
    });
  }

  /**
   * パーティクルエフェクトの生成
   */
  function generateParticles() {
    var $particlesContainer = $(".particles-container");

    // コンテナが存在する場合のみ実行
    if ($particlesContainer.length === 0) return;

    for (var i = 1; i <= 20; i++) {
      var $particle = $('<div class="particle"></div>');

      // ランダムな位置、サイズ、透明度を設定
      var size = Math.floor(Math.random() * 10) + 5;
      var posX = Math.floor(Math.random() * 100);
      var posY = Math.floor(Math.random() * 100);
      var opacity = Math.random() * 0.5 + 0.1;
      var delay = Math.random() * 15;
      var duration = Math.random() * 20 + 10;

      $particle.css({
        width: size + "px",
        height: size + "px",
        left: posX + "%",
        top: posY + "%",
        opacity: opacity,
        animationDelay: delay + "s",
        animationDuration: duration + "s",
      });

      $particlesContainer.append($particle);
    }
  }

  /**
   * コースカードのホバーエフェクト
   */
  function initCourseCardEffects() {
    $(".course-card").hover(
      function () {
        $(this).find(".card-image img").css("transform", "scale(1.05)");
      },
      function () {
        $(this).find(".card-image img").css("transform", "scale(1)");
      }
    );
  }

  /**
   * プログレスバーのアニメーション
   */
  function animateProgressBar() {
    $(".progress-bar .progress").each(function () {
      var width = $(this).data("width");
      if (width) {
        $(this)
          .css("width", "0%")
          .animate(
            {
              width: width + "%",
            },
            1000
          );
      }
    });
  }

  /**
   * スクロール時のアニメーション
   */
  function initScrollAnimations() {
    // 初回実行
    animateOnScroll();

    // スクロールイベント
    $(window).on("scroll", function () {
      animateOnScroll();
    });
  }

  function animateOnScroll() {
    $(
      ".course-card, .feature-item, .section-title, .lesson-item, .content-item"
    ).each(function () {
      var elementTop = $(this).offset().top;
      var elementVisible = 150;
      var windowHeight = $(window).height();
      var scrollPos = $(window).scrollTop();

      if (scrollPos > elementTop - windowHeight + elementVisible) {
        $(this).addClass("animated");
      }
    });
  }
})(jQuery);

/**
 * モバイルメニューの動作用JavaScript
 */
document.addEventListener("DOMContentLoaded", function () {
  const menuToggle = document.querySelector(".menu-toggle");
  const menuContainer = document.querySelector(".menu-container");

  if (menuToggle && menuContainer) {
    menuToggle.addEventListener("click", function () {
      menuContainer.classList.toggle("active");
      const expanded =
        menuToggle.getAttribute("aria-expanded") === "true" || false;
      menuToggle.setAttribute("aria-expanded", !expanded);
    });
  }

  // サブメニューの開閉（モバイル用）
  if (window.innerWidth <= 992) {
    const hasChildren = document.querySelectorAll(
      ".menu-item-has-children > a"
    );

    hasChildren.forEach(function (item) {
      const dropdownToggle = document.createElement("span");
      dropdownToggle.className = "dropdown-toggle";
      dropdownToggle.innerHTML = '<i class="fas fa-chevron-down"></i>';
      item.appendChild(dropdownToggle);

      dropdownToggle.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
        const parent = this.parentNode.parentNode;
        const subMenu = parent.querySelector(".sub-menu");
        subMenu.classList.toggle("active");
        this.classList.toggle("toggled");
      });
    });
  }

  // ウィンドウのリサイズ時にメニューをリセット
  window.addEventListener("resize", function () {
    if (window.innerWidth > 992 && menuContainer) {
      menuContainer.classList.remove("active");
      if (menuToggle) {
        menuToggle.setAttribute("aria-expanded", "false");
      }

      // サブメニューのリセット
      const activeSubMenus = document.querySelectorAll(".sub-menu.active");
      const toggledButtons = document.querySelectorAll(
        ".dropdown-toggle.toggled"
      );

      activeSubMenus.forEach(function (menu) {
        menu.classList.remove("active");
      });

      toggledButtons.forEach(function (button) {
        button.classList.remove("toggled");
      });
    }
  });
});
