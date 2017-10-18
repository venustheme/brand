/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
 var config = {
 	map: {
 		"*": {
 			vesallOwlCarousel: "Ves_All/lib/owl.carousel/owl.carousel.min",
 			vesallBootstrap: "Ves_All/lib/bootstrap/js/bootstrap.min",
 			vesallColorbox: "Ves_All/lib/colorbox/jquery.colorbox.min",
 			vesallFancybox: "Ves_All/lib/fancybox/jquery.fancybox.pack",
 			vesallFancyboxMouseWheel: "Ves_All/lib/fancybox/jquery.mousewheel-3.0.6.pack"
 		}
 	},
 	shim: {
        'Ves_All/lib/bootstrap/js/bootstrap.min': {
            'deps': ['jquery']
        },
        'Ves_All/lib/bootstrap/js/bootstrap': {
            'deps': ['jquery']
        },
        'Ves_All/lib/owl.carousel/owl.carousel': {
            'deps': ['jquery']
        },
        'Ves_All/lib/owl.carousel/owl.carousel.min': {
        	'deps': ['jquery']
        },
        'Ves_All/lib/fancybox/jquery.fancybox': {
            'deps': ['jquery']  
        },
        'Ves_All/lib/fancybox/jquery.fancybox.pack': {
            'deps': ['jquery']  
        },
        'Ves_All/lib/colorbox/jquery.colorbox': {
            'deps': ['jquery']  
        },
        'Ves_All/lib/colorbox/jquery.colorbox.min': {
            'deps': ['jquery']  
        }
    }
 };