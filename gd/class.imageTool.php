<?php

//	$OneArr = getimagesize('./123.jpg');
//	$w = $OneArr[0];
//	$h = $OneArr[1];
//
//	var_dump($OneArr);
//	var_dump($OneArr[2]);
//	
//	echo image_type_to_mime_type($OneArr[0]);
//	image_type_to_mime_type();  // 类型
		
	// 水印：把指定的水印复制到目标上，并加透明效果
	// 缩略图：把大图片复制到小尺寸画面上		
		
	class ImageTool {
		
		/**
		 * 分析图片的信息
		 * @param {String} $image 图片路径 
		 * @return {mixin} Array Boolean
		 */
		protected static function imageInfo( $image ) {
			
			// 判断图片是否存在
			if(!file_exists($image)) {
				return false;
			}
			$info = getimagesize($image);
			if($info == false) {
				return false;
			}
			
			// 此时info分析出来，是数组
			$img['width'] = $info[0];
			$img['height'] = $info[1];
			$img['ext'] = substr($info['mime'] ,strpos($info['mime'], '/')+1); // 后缀
			
			return $img;			
		}	
		
		/**
		 * 加水印
		 * @param {String} $dst 目标图片
		 * @param {String} $water 水印小图片
		 * @param {String} $save 存储图片位置   默认替换原始图
		 * @param {Int} $alpha 透明度
		 * @param {Int} $pos 水印位置
		 * @return {Boolean} 添加水印是否成功
		 */
		public static function addWater($dst, $water, $save=NULL, $pos=4, $alpha=50) {
			 
			// 保证二个文件是否存在
			if(!file_exists($dst) || !file_exists($water)) {
				return false;
			}
			
			$dstInfo = self::imageInfo($dst); // 读取图片信息
			$waeterInfo = self::imageInfo($water); // 读取图片信息 
			
			// 水印不能比待操作图片大
			if( $waeterInfo['height'] > $dstInfo['height'] || $waeterInfo['width'] > $dstInfo['width'] ) {
				return false;
			}
				
			// 两张图片读取到画布上, 使用处理  动态函数读取
			$dFun = 'imagecreatefrom' . $dstInfo['ext'];
			$wFun = 'imagecreatefrom' . $dstInfo['ext'];
			
			// 是否存在函数
			if ( !function_exists($dFun) || !function_exists($wFun) ) {
				return false;
			}
			
			// 动态加载函数创建画布
			$dIm = $dFun($dst); // 创建待操作的画布
			$wIm = $wFun($water); // 创建水印画布
			
			// 处理水印的位置 计算粘贴的坐标
			switch ($pos) {
				case 0: // 左上角
					$posX = 0;
					$posY = 0;
					break;
				case 1: // 右上角
					$posX = $dstInfo['width'] - $waeterInfo['width'];
					$poxY = 0;
					break;
				case 2: // 居中
					$posX = ($dstInfo['width'] - $waeterInfo['width']) / 2;
					$posY = ($dstInfo['height'] - $waeterInfo['height']) / 2;
					break; 
				case 3: // 左下角
					$posX = 0;
					$posY = $dstInfo['height'] - $waeterInfo['height'];
					break;
				case 4: // 右下角
					$posX = $dstInfo['width'] - $waeterInfo['width'];
					$posY = $dstInfo['height'] - $waeterInfo['height'];
					break;
				
				case 5: // 底部中间
					$posX = ($dstInfo['width'] - $waeterInfo['width']) / 2;
					$posY = $dstInfo['height'] - $waeterInfo['height'];
					break;
			}
									
			// 加水印
			imagecopymerge($dIm, $wIm, $posX, $posY, 0, 0, $waeterInfo['width'], $waeterInfo['height'], $alpha);
						
			// 保存
			if (!$save) {
				$save = $dst;
				unlink($dst); // 删除原图片
			}
			
			// 生成水印
			$createFun = 'image' . $dstInfo['ext'];
			$createFun($dIm, $save);
				
			imagedestroy($dIm);
			imagedestroy($wIm);
			
			return true;			
		}	
		
		/**
		 * thumb 生成缩略图
		 * @param {String} $dst 原始路径
		 * @param {String} $save 保存路径
		 * @param {Int} $width 缩略图 宽度
		 * @param {Int} $height 缩略图 高度
		 */
		public static function thumb( $dst, $save=NULL, $width=200, $height=200 ) {
			
		} 
		
	}
	
	echo ImageTool::addWater('./ellipse.jpeg', './hao.jpg', 'pink.jpg') ? 'ok' : 'no';
	echo ImageTool::addWater('./ellipse.jpeg', './hao.jpg', 'pink1.jpg', 2) ? 'ok' : 'no';
	echo ImageTool::addWater('./ellipse.jpeg', './hao.jpg', 'pink2.jpg', 5) ? 'ok' : 'no';
	
?>

