(function (_0x5e5bx1) {
    ("use strict");
    _0x5e5bx1(window).on("load", function () {
        _0x5e5bx1('[data-loader="circle-side"]').fadeOut();
        _0x5e5bx1("#preloader").delay(350).fadeOut("slow");
        _0x5e5bx1("body").delay(350).css({ overflow: "visible" });
    });
    _0x5e5bx1("form#wrapped").on("submit", function () {
        var _0x5e5bx2 = _0x5e5bx1("form#wrapped");
        _0x5e5bx2.validate();
        if (_0x5e5bx2.valid()) {
            _0x5e5bx1("#loader_form").fadeIn();
        }
    });
    _0x5e5bx1(".styled-select select").niceSelect();
    _0x5e5bx1("#password1, #password2").hidePassword("focus", { toggle: { className: "my-toggle" } });
    _0x5e5bx1('input[type="range"]').rangeslider({
        polyfill: false, onInit: function () {
            this.output = _0x5e5bx1(".budget_slider span").html(this.$element.val());
        }, onSlide: function (_0x5e5bx3, _0x5e5bx4) {
            this.output.html(_0x5e5bx4);
        }
    });
    _0x5e5bx1('a[href^="#"].mobile_btn').on("click", function (_0x5e5bx5) {
        _0x5e5bx5.preventDefault();
        var _0x5e5bx6 = this.hash;
        var _0x5e5bx7 = _0x5e5bx1(_0x5e5bx6);
        _0x5e5bx1("html, body").stop().animate({ scrollTop: _0x5e5bx7.offset().top }, 400, "swing", function () {
            window.location.hash = _0x5e5bx6;
        });
    });
    var _0x5e5bx8 = _0x5e5bx1(".cd-overlay-nav"), _0x5e5bx9 = _0x5e5bx1(".cd-overlay-content"), _0x5e5bxa = _0x5e5bx1(".cd-primary-nav"), _0x5e5bxb = _0x5e5bx1(".cd-nav-trigger");
    _0x5e5bxc();
    _0x5e5bx1(window).on("resize", function () {
        window.requestAnimationFrame(_0x5e5bxc);
    });
    _0x5e5bxb.on("click", function () {
        if (!_0x5e5bxb.hasClass("close-nav")) {
            _0x5e5bxb.addClass("close-nav");
            _0x5e5bx8.children("span").velocity({ translateZ: 0, scaleX: 1, scaleY: 1 }, 500, "easeInCubic", function () {
                _0x5e5bxa.addClass("fade-in");
            });
        } else {
            _0x5e5bxb.removeClass("close-nav");
            _0x5e5bx9.children("span").velocity({ translateZ: 0, scaleX: 1, scaleY: 1 }, 500, "easeInCubic", function () {
                _0x5e5bxa.removeClass("fade-in");
                _0x5e5bx8.children("span").velocity({ translateZ: 0, scaleX: 0, scaleY: 0 }, 0);
                _0x5e5bx9.addClass("is-hidden").one("webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend", function () {
                    _0x5e5bx9.children("span").velocity({ translateZ: 0, scaleX: 0, scaleY: 0 }, 0, function () {
                        _0x5e5bx9.removeClass("is-hidden");
                    });
                });
                if (_0x5e5bx1("html").hasClass("no-csstransitions")) {
                    _0x5e5bx9.children("span").velocity({ translateZ: 0, scaleX: 0, scaleY: 0 }, 0, function () {
                        _0x5e5bx9.removeClass("is-hidden");
                    });
                }
            });
        }
    });
    function _0x5e5bxc() {
        var _0x5e5bxd = Math.sqrt(Math.pow(_0x5e5bx1(window).height(), 2) + Math.pow(_0x5e5bx1(window).width(), 2)) * 2;
        _0x5e5bx8.children("span").velocity({ scaleX: 0, scaleY: 0, translateZ: 0 }, 50).velocity({ height: _0x5e5bxd + "px", width: _0x5e5bxd + "px", top: -(_0x5e5bxd / 2) + "px", left: -(_0x5e5bxd / 2) + "px" }, 0);
        _0x5e5bx9.children("span").velocity({ scaleX: 0, scaleY: 0, translateZ: 0 }, 50).velocity({ height: _0x5e5bxd + "px", width: _0x5e5bxd + "px", top: -(_0x5e5bxd / 2) + "px", left: -(_0x5e5bxd / 2) + "px" }, 0);
    }
}(window.jQuery));
