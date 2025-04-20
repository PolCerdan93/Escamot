import $, { event } from "jquery";
import Swiper from 'swiper/bundle';
import 'swiper/css/bundle';



export default function() {

    var Application = {};

    Application.home = {
        init: function() {
            var self = this
            $(window).resize(function() {
                console.log("resize callbacks");
            });
            self.homeSlider.init($('#homeSlider'));
            self.newsletter();
        },
        homeSlider: {
            init: function($module) {
                this.initSwiper($module);
                this.getmouseWheel($module);
            },
            TransitionisActive: false,
            getmouseWheel: function($module) {
                var supportsWheel = false;

                function DoSomething(e) {
                    if (!Application.home.homeSlider.TransitionisActive) {
                        Application.home.homeSlider.TransitionisActive = true;
                        if (e.type == "wheel") supportsWheel = true;
                        else if (supportsWheel) return;
                        var delta = ((e.deltaY || -e.wheelDelta || e.detail) >> 10) || 1;
                        var HomeSwiperInstance = $module[0].swiper;
                        if (delta == -1) {
                            HomeSwiperInstance.slidePrev();
                            setTimeout(function() {
                                Application.home.homeSlider.TransitionisActive = false;
                            }, 300);
                        } else if (delta == 1) {
                            HomeSwiperInstance.slideNext();
                            setTimeout(function() {
                                Application.home.homeSlider.TransitionisActive = false;
                            }, 300);
                        }
                    }
                }
                document.addEventListener('wheel', DoSomething);
                document.addEventListener('mousewheel', DoSomething);
                document.addEventListener('DOMMouseScroll', DoSomething);
            },

            initSwiper: function($module) {
                console.log($module);
                
                var swiper = new Swiper($module[0], {
                    direction: "vertical",
                    speed: 1000,
                    parallax: true,
                    pagination: {
                        el: ".swiper-pagination-vertical",
                        clickable: true,
                    },
                    on: {
                        init: function () {

                            var swiper2 = new Swiper($module.find(".inset-horizontal-swiper .horizontalSwiper")[0], {
                                speed: 1000,
                                parallax: true,
                                navigation: {
                                    nextEl: ".swiper-button-next-horitzontal",
                                    prevEl: ".swiper-button-prev-horitzontal",
                                },
                                pagination: {
                                  el: ".swiper-pagination-horitzontal",
                                  dynamicBullets: true,
                                  clickable: true,
                                },
                                on: {
                                    init: function () {
                                        console.log("inicialitzat el swiper horitzontal!")
                                    }
                                },
                              });
                             
                        },
                        slideNextTransitionStart : function(swiper){
                            Application.header.changeActiveNavbar(swiper.activeIndex)
                            
                        },
                        slidePrevTransitionStart : function(swiper){
                            Application.header.changeActiveNavbar(swiper.activeIndex)
                        }
                      },
                });

                
                
                

            }
           
        },
        newsletter : function(){
            $("#newsletterForm").find("button").on("click",function(){
                Application.home.subscribeNewsletter();
            });
            $("#newsletterForm").find("form").on("submit",function(){
                event.preventDefault();
                Application.home.subscribeNewsletter();
            });
        },
        subscribeNewsletter : function(){
            var mailvalue = $("#newsletterForm").find("#form_email").val();
            console.log(Application.utils.validateEmail(mailvalue));
            if(Application.utils.validateEmail(mailvalue)!=null){
                
                $.ajax({
                    url: '/registrenewsletter',
                    method: 'POST',
                    data: {mail : mailvalue }
                }).then(function(response) {
                    console.log(response);
                    Application.utils.displayMessageSuccess("Correu electònic subscrit correctament");
                });

                
            }else{
                Application.utils.displayMessageDanger("El Correu electònic introduit no es vàlid");
            }
        }
    }
    Application.header = {
        init: function() {
            var self = this
            $(window).resize(function() {
                console.log("resize callbacks");
            });
            self.menu($('header'));
        },
        menu : function($module){
            $module.find("nav ul li").on("click", function($this){
                
                const swiper = document.querySelector('#homeSlider').swiper;
                swiper.slideTo($(this).data("order"),1500)
                //Application.header.changeActiveNavbar($(this).data("order"));
            });
        },
        changeActiveNavbar: function($idx) {
            if($idx==1){
                $("header").addClass("Dark");
            }else{
                $("header").removeClass("Dark");
            }
            $("nav li").removeClass("active");
            $("nav li").eq($idx).addClass("active");
        }
    },
    Application.utils = {
        displayMessageSuccess : function(message){
            $("body").append( "<div class='alert-message-success'><div class='wrap'>"+ message +"</p>");
            $(".alert-message-success").fadeIn();
            setTimeout(function() { $(".alert-message-success").fadeOut(); }, 5000);
        },
        displayMessageDanger : function(message){
            $("body").append( "<div class='alert-message-danger'><div class='wrap'>"+ message +"</p>");
            $(".alert-message-danger").fadeIn();
            setTimeout(function() { $(".alert-message-danger").fadeOut(); }, 5000);
        },
        validateEmail : function(email){
            return email.match(
              /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
            );
        },
    },
    Application.module = {
        init: function() {
            $(function() {
                $('[data-module]').each(function() {
                    console.log($(this).attr('data-module'));
                    Application.module[$(this).attr('data-module')]($(this));
                });
            });
        },
        form: function($module) {
            var self = this;
            $module.find("input").on('keyup keypress blur change', function(e) {
                self.checkEnteredText($(this));
            });
            $module.find("input").each(function() {
                self.checkEnteredText($(this));
                $(this).parent().prepend($(this).detach());
            });
            $module.find("button").each(function() {
                $(this).removeClass();
                $(this).addClass("btn btn-dark btn-block form-control");
            });

            var desplegable = $module.find(".select-transformation").data("replacedelement");
            $module.find(".select-transformation .option").on("click", function() {
                var $parentChildren = $(this).parent().parent().children();
                $parentChildren.each(function(element, self) {
                    $(self).find(".option").removeClass("active");
                });
                $(this).addClass("active");
                var optiontoCheck = $(this).data("value");
                $(desplegable).val(optiontoCheck);
            });
            if (desplegable) {
                $(desplegable).parent().hide();
                $module.find(".select-transformation").detach().insertBefore($(desplegable).parent().parent().find("Button[type=submit]"));
            }
            $module.find("form>div").removeClass("text-entered");
            console.log($module.find("input[type=file]"));
            $module.find("input[type=file]").change( function(){
                    var preview = $(this).data("vinculated-img");
                    var file    = $(this)[0].files[0];
                    var reader  = new FileReader();
                    reader.onloadend = function () {
                        $(preview).empty();
                        var src = reader.result;
                        var img = $('<div class="transformpchilddiv"><img class="transferpicture" src="'+src+'" /></div>'); 
                        
                        img.appendTo(preview);
                    }
                    console.log(file);
                    if (file) {
                      reader.readAsDataURL(file);
                    } else {
                      preview.src = "";
                    }
            });

            $module.animate({ opacity: 1 }, 1000);
        },
        checkEnteredText: function($module) {
            if ($module.val().length > 0) {
                $module.parent().addClass("text-entered");
            } else {
                $module.parent().removeClass("text-entered");
            }
        },
        aceEditor : function($module) {

            var ace = require('brace');
            var editor = Array();
            $module.find(".js-editor-selector").each(function(index) {
                editor[index] = ace.edit('javascript-editor' + index);
                editor[index].getSession().setMode('ace/mode/javascript');
                editor[index].setTheme('ace/theme/monokai');
                editor[index].getSession().setUseWorker(false);
            });
            $module.find('form').on('submit', function() {
                $module.find(".js-editor-selector").each(function(index) {
                    var input = $(this).data("vinculatedinput");
                    $(input).val(editor[index].getValue());
                });
                return true;
            });
        },
        alertMessages: function($module) {
            $module.fadeIn();
            setTimeout(function() { $module.fadeOut(); }, 5000);
        }
    }
    return Application;
};