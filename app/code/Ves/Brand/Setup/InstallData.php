<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Ves_Brand
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\Brand\Setup;

use Ves\Brand\Model\Brand;
use Ves\Brand\Model\BrandFactory;
use Ves\Brand\Model\Group;
use Ves\Brand\Model\GroupFactory;
use Magento\Framework\Module\Setup\Migration;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
	/**
	 * Brand Factory
	 *
	 * @var BrandFactory
	 */
	private $brandFactory;

	/**
	 * @param BrandFactory $brandFactory 
	 * @param GroupFactory $groupFactory 
	 */
	public function __construct(
		BrandFactory $brandFactory,
		GroupFactory $groupFactory
		)
	{
		$this->brandFactory = $brandFactory;
		$this->groupFactory = $groupFactory;
	}

	public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
	{
		$groups = [
		[
			'name' => 'Fashion',
			'url_key' => 'fashion',
			'position' => '1',
			'status' => '1',
			'show_in_sidebar' => '1'
		],
		[
			'name' => 'Kitchen',
			'url_key' => 'kitchen',
			'position' => '2',
			'status' => '1',
			'show_in_sidebar' => '1'
		]
		];

		/**
         * Insert default and groups
         */
		foreach ($groups as $data) {
			$this->groupFactory->create()->setData($data)->save();
		}

		$brands = [
			[
				'name' => 'Megashop',
				'url_key' => 'megashop',
				'description' => '<p>Inspired by the catwalk and designed in Manchester, Amalie &amp; Amber will effortlessly bring style points to your wardrobe. Explore flirty florals, lavish lace, co-ordinating pieces and style essentials that will take you from desk to dinner and onto the party.</p>',
				'group_id' => '1',
				'image' => 'ves/brand/maxshop.jpg',
				'thumbnail' => 'ves/brand/brand1.png',
				'page_title' => 'Megashop',
				'meta_keywords' => 'shop, fashion',
				'meta_description' => 'Inspired by the catwalk and designed in Manchester, Amalie & Amber',
				'page_layout' => '2columns-left',
				'status' => '1',
				'position' => '0',
				'stores' => [0]
			],
			[
				'name' => 'FloralStore',
				'url_key' => 'floralstore',
				'description' => '<p>Founded in 1998, AX Paris brings catwalk looks to the high-street while remaining one stiletto ahead of the fashion game. Our selection of AX Paris is packed with striking body-con dresses, statement maxis, bright prints and vibrant colours that are perfect for day-to-night dressing and special occasions.</p>',
				'group_id' => '1',
				'image' => 'ves/brand/floralstore.png',
				'thumbnail' => 'ves/brand/brand2.png',
				'page_title' => 'FloralStore',
				'meta_keywords' => 'shop, fashion, floral',
				'meta_description' => 'Founded in 1998, AX Paris brings catwalk looks to the high-street while remaining one stiletto ahead of the fashion game.',
				'page_layout' => '1column',
				'status' => '1',
				'position' => '1',
				'stores' => [0]
			],
			[
				'name' => 'Sexy Trends',
				'url_key' => 'sexy-trends',
				'description' => 'Designed for newborns and babies up to 24 months, Babaluno clothing keeps baby stylish in comfort. Made from the softest fabrics for delicate skin, the collection includes pretty dresses, floral t-shirts and cool denim.',
				'group_id' => '1',
				'image' => 'ves/brand/sexytrend.jpg',
				'thumbnail' => 'ves/brand/brand3.png',
				'page_title' => 'Sexy Trends',
				'meta_keywords' => 'sexy, shop, fashion, trend',
				'meta_description' => 'Designed for newborns and babies up to 24 months, Babaluno clothing keeps baby stylish in comfort.',
				'page_layout' => '2columns-right',
				'status' => '1',
				'position' => '2',
				'stores' => [0]
			],
			[
				'name' => 'Clother',
				'url_key' => 'clother',
				'description' => '<p>Created in USA 1963, Babeskin swimwear has been specifically designed for kids who love adrenaline sports. Whether it&rsquo;s a practical tankini, vibrant dress or pretty swimsuit for little ones, Babeskin has everything you need for her love of water.</p>',
				'group_id' => '1',
				'image' => 'ves/brand/clothers.jpg',
				'thumbnail' => 'ves/brand/brand41.png',
				'page_title' => 'Clother',
				'meta_keywords' => 'clother, women, men, kids',
				'meta_description' => 'Created in USA 1963, Babeskin swimwear has been specifically designed for kids who love adrenaline sports.',
				'page_layout' => '2columns-left',
				'status' => '1',
				'position' => '3',
				'stores' => [0]
			],
			[
				'name' => 'Boutique',
				'url_key' => 'boutique',
				'description' => '<p>Kit them out in this season&rsquo;s coolest designs with the SS15 collection from Charlie&amp;me. Inspired by fun in the park and playful days, the collection features graphic tees, bright shorts and sturdy shoes for boys and pretty dresses, summery floral prints and partywear for girls. Look out for bodysuits and booties for little ones too.</p>',
				'group_id' => '1',
				'image' => 'ves/brand/boutique.jpg',
				'thumbnail' => 'ves/brand/brand5.png',
				'page_title' => 'Boutique',
				'meta_keywords' => 'clother, women, men, kids',
				'meta_description' => 'Kit them out in this season&rsquo;s coolest designs with the SS15 collection from Charlie.',
				'page_layout' => '2columns-left',
				'status' => '1',
				'position' => '4',
				'stores' => [0]
			],
			[
				'name' => 'One Shop',
				'url_key' => 'one-shop',
				'description' => 'These lightweight shoes are perfect for relaxed weekend wear.',
				'group_id' => '1',
				'image' => 'ves/brand/oneshop.jpg',
				'thumbnail' => 'ves/brand/brand6.png',
				'page_title' => 'One Shop',
				'meta_keywords' => 'candy, milk, fashion, clother, men',
				'meta_description' => 'These lightweight shoes are perfect for relaxed weekend wear.',
				'page_layout' => '1column',
				'status' => '1',
				'position' => '5',
				'stores' => [0]
			],
			[
				'name' => 'Nextstore',
				'url_key' => 'nextstore',
				'description' => 'Fresh out of the brand\'s London design house, our collection of Cutie clothing is packed with creative designs and unique detailing. Shop Cutie\'s pretty bodycon dresses, skater dresses and shift dresses in flattering cuts and a vibrant colour palette.',
				'group_id' => '1',
				'image' => 'ves/brand/nextstore.jpg',
				'thumbnail' => 'ves/brand/brand7.png',
				'page_title' => 'Next Store',
				'meta_keywords' => 'funny, killy',
				'meta_description' => 'Fresh out of the brand\'s London design house, our collection of Cutie clothing is packed with creative designs and unique detailing.',
				'page_layout' => '2columns-left',
				'status' => '1',
				'position' => '6',
				'stores' => [0]
			],
			[
				'name' => 'Big Shop',
				'url_key' => 'big-shop',
				'description' => 'Emma-Jane has been the UK\'s favourite brand of maternity and nursing lingerie for over 25 years. Specialising exclusively in maternity wear, Emma-Jane offers the very latest designs and incorporates the latest technology to provide the most practical solutions for pregnancy and motherhood.',
				'group_id' => '1',
				'image' => 'ves/brand/bigshop.jpg',
				'thumbnail' => 'ves/brand/brand8.png',
				'page_title' => 'Big Shop',
				'meta_keywords' => 'kids, killy',
				'meta_description' => 'Emma-Jane has been the UK\'s favourite brand of maternity and nursing lingerie for over 25 years.',
				'page_layout' => '2columns-left',
				'status' => '1',
				'position' => '7',
				'stores' => [0]
			],
			[
				'name' => 'Stationery',
				'url_key' => 'stationery',
				'description' => 'Harry Brown formalwear has got the London look, with elegant sartorial details and impeccable attention to detail. Each garment is carefully engineered with refined fit, form and function in mind. The cut is tailored for a truly modern silhouette',
				'group_id' => '1',
				'image' => 'ves/brand/stationnary.jpg',
				'thumbnail' => 'ves/brand/brand9.png',
				'page_title' => 'Stationery',
				'meta_keywords' => 'kids, killy',
				'meta_description' => 'Harry Brown formalwear has got the London look, with elegant sartorial details and impeccable attention to detail.',
				'page_layout' => '2columns-left',
				'status' => '1',
				'position' => '8',
				'stores' => [0]
			],
			[
				'name' => 'Maxx Shop',
				'url_key' => 'maxx-shop',
				'description' => 'With over 100 years in the fashion and sporting arena, Gola’s strong links and impressive legacy can be seen in every collection. Streetwear trends are interpreted into high quality, wearable designs and key sporting pieces, reinforcing the brand’s signature: unique designs in a spectrum of colours and distinctive upper fabrics and materials.',
				'group_id' => '1',
				'image' => 'ves/brand/maxxshop.jpg',
				'thumbnail' => 'ves/brand/brand10.png',
				'page_title' => 'Maxx Shop',
				'meta_keywords' => 'women, men',
				'meta_description' => 'With over 100 years in the fashion and sporting arena, Gola’s strong links and impressive legacy can be seen in every collection.',
				'page_layout' => '2columns-left',
				'status' => '1',
				'position' => '9',
				'stores' => [0]
			],
			[
				'name' => 'Car Market',
				'url_key' => 'car-market',
				'description' => '<p><span>Ice Blossom never fails to deliver feminine pieces for every occasion. This season\'s party dresses from Ice Blossom include one shoulder dresses, elegant lace, sequins and bejewelled dresses with beautiful beading for glamorous evenings.</span></p>',
				'group_id' => '1',
				'image' => 'ves/brand/carmarket.jpg',
				'thumbnail' => 'ves/brand/brand61.png',
				'page_title' => 'Car Market',
				'meta_keywords' => 'women, men',
				'meta_description' => '<span>Ice Blossom never fails to deliver feminine pieces for every occasion.</span>',
				'page_layout' => '2columns-left',
				'status' => '1',
				'position' => '10',
				'stores' => [0]
			],
			[
				'name' => 'Mobile',
				'url_key' => 'mobile',
				'description' => 'Playtex believes that ‘Feeling better than ever’ starts with wearing the right bra. Supportive in the right places and with unique comfort features, Playtex bras in a range of feminine colours and styles combine style with support to make the most of your silhouette.',
				'group_id' => '1',
				'image' => 'ves/brand/mobile.jpg',
				'thumbnail' => 'ves/brand/brand71.png',
				'page_title' => 'Mobile',
				'meta_keywords' => 'women, men',
				'meta_description' => 'Playtex believes that ‘Feeling better than ever’ starts with wearing the right bra.',
				'page_layout' => '2columns-left',
				'status' => '1',
				'position' => '11',
				'stores' => [0]
			],
			[
				'name' => 'Next Store',
				'url_key' => 'next-store',
				'description' => 'Brighten up your little one\'s wardrobe with the latest pieces from Tricky Tracks. With cosy jackets, bright all-in-ones, pretty dresses and patterned tops, Tricky Tracks clothing is great for your growing baby.',
				'group_id' => '1',
				'image' => 'ves/brand/nextstore1.jpg',
				'thumbnail' => 'ves/brand/brand81.png',
				'page_title' => 'Next Store',
				'meta_keywords' => 'women, men',
				'meta_description' => 'Brighten up your little one\'s wardrobe with the latest pieces from Tricky Tracks.',
				'page_layout' => '2columns-left',
				'status' => '1',
				'position' => '12',
				'stores' => [0]
			],
			[
				'name' => 'Sharp',
				'url_key' => 'sharp',
				'description' => 'Brighten up your little one\'s wardrobe with the latest pieces from Tricky Tracks. With cosy jackets, bright all-in-ones, pretty dresses and patterned tops, Tricky Tracks clothing is great for your growing baby.',
				'group_id' => '2',
				'thumbnail' => 'ves/brand/sharp.jpg',
				'page_layout' => '2columns-left',
				'status' => '1',
				'position' => '12',
				'stores' => [0]
			]
		];

		/**
         * Insert default and brand
         */
		foreach ($brands as $data) {
			$this->brandFactory->create()->setData($data)->save();
		}

		
	}
	
}