<?php

/**
 * @package   yii2-dialog
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2018
 * @version   1.0.5
 */

namespace Avers\main;

use common\models\File;
use common\models\User;
use hoomanMirghasemi\jdf\Jdf;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\helpers\Url;

/**

 * ```
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class Main extends ActiveRecord
{

    const MOST_VISIT = 10;
    const MOST_PAPULAR = 1;


    const NO_IMAGE_ID = 1;

    const STATUS_PENDING = 1;
    const STATUS_PUBLISH = 2;
    const STATUS_DISABLE = 3;
    const STATUS_DELETED= 4;

    const RATE = 7;


    const MOD_MENU = 1;
    const MOD_SLIDER = 2;
    const MOD_CATEGORY = 3;
    const MOD_CATEGORY_TAB = 4;
    const MOD_SPECIAL_OFFER = 5;
    const MOD_MOST_VISIT = 6;
    const MOD_OUR_SERVICES = 7;
    const MOD_NEWSLETTER = 8;
    const MOD_BRAND = 9;
    const MOD_RELATED_PRODUCT = 10;
    const MOD_COMMENT = 11;
    const MOD_CONTACT = 12;
    const MOD_FAVORITE_PRODUCT = 13;
    const MOD_CUSTOM_PRODUCT = 14;
    const MOD_CUSTOM_HTML = 15;
    const MOD_PACKAGE = 16;
    const MOD_LAST_PRODUCT = 17;
    const MOD_PRODUCT_CUSTOM_CATEGORY = 18;

    const POS_1 = 1;
    const POS_2 = 2;
    const POS_3 = 3;
    const POS_4 = 4;
    const POS_5 = 6;
    const POS_6 = 7;
    const POS_7 = 8;
    const POS_MENU = 5;
    const POS_MOBILE_MENU = 9;
    const ITEM_PRODUCT = 1;
    const ITEM_PRODUCT_CATEGORY = 2;
    const ITEM_EXTERNAL_LINK = 3;
    const ITEM_BRAND = 4;
    const STATUS_ACTIVE = 1;
    const STATUS_DISACTIVE = 0;

    public static function isStationaryTheme()
    {
        $theme = Theme::findOne(['status' => 1]);
        if ($theme->title == 'stationary') {
            return true;
        }
        return false;
    }

    public static function Convert_date($string_date)
    {
        if ($string_date != null) {
            $date = explode('/', $string_date);
            $epoch_time = strtotime(Jdf::jalali_to_gregorian($date[0], $date[1], $date[2], '/'));
        } else {
            $epoch_time = null;
        }
        return $epoch_time;
    }

    public static function generateSlug($title, $type = null)
    {
        if (is_null($type)) {
            $result = str_replace(' ', '-', trim($title));
        } else {
            $result = str_replace(' ', '-', trim($type . $title));
        }

        return $result;
    }
    public static function getTheme()
    {
        $theme = Theme::findOne(['status' => 1]);
        return isset($theme->title) ? $theme->title : '';
    }

    public static function getTotalPriceTala($price, $weight = null, $wage = 0, $fee = 0, $userType = null)
    {
        $final = '';
        $per_gram = ($weight * $price);//قیمت هرگرم طلا
        $per_wage = ($weight * ($wage));//کارمزد
        $percent = (($price * $fee) / 100);
        $per_fee = ($weight * $percent);//اجرت
        $calculate = ($per_gram + $per_fee + $per_wage);
        $calculate = round($calculate);

        switch ($userType) {
            case User::ROLE_USER :
                $user_percent = ($calculate * self::RATE) / 100;
                $final = $calculate + $user_percent;
                break;
            case User::ROLE_WHOLESALER || User::ROLE_ADMIN:
                $final = $calculate;

                break;
        }

        return (round($final));
    }


    public static function getStatusArray()
    {
        return [

            self::STATUS_PUBLISH => Yii::t('app', 'PUBLISH'),
            self::STATUS_DISABLE => Yii::t('app', 'DISABLE'),
        ];
    }

    public function getPositions()
    {
        return [
            self::POS_1 => Yii::t('app', 'Position 1'),
            self::POS_2 => Yii::t('app', 'Position 2'),
            self::POS_3 => Yii::t('app', 'Position 3'),
            self::POS_4 => Yii::t('app', 'Position 4'),
            self::POS_5 => Yii::t('app', 'Position 5'),
            self::POS_6 => Yii::t('app', 'Position 6'),
            self::POS_7 => Yii::t('app', 'Position 7'),
            self::POS_MENU => Yii::t('app', 'Main Menu'),
            self::POS_MOBILE_MENU => Yii::t('app', 'Mobile Menu'),
        ];
    }

    public function getPositionName($position = null)
    {
        if ($position === null)
            if ($this->position == null)
                return null;
        $position = $this->position;

        return $this->positions[$position];
    }

    public function getPosName($position)
    {
        return $this->positions[$position];

    }

    public function getModules($position)
    {
        $theme = Theme::find()->where(['status' => '1'])->one();

        if ($theme->title == 'digikala' && ($position == Module::POS_1 || $position == Module::POS_2 || $position == Module::POS_3|| $position == Module::POS_5)) {
            $themeModule = ThemeModule::find()
                ->joinWith('module')
                ->where(['theme_id' => $theme->id])
                ->andWhere(['!=', 'name', 'Related Product'])
                ->andWhere(['!=', 'name', 'Comment'])
                ->andWhere(['!=', 'name', 'Package'])
                ->andWhere(['!=', 'name', 'Tab Category'])
                ->all();
            $arr = [];
            foreach ($themeModule as $module) {
                $arr[$module->module_id] = Yii::t('app', $module->module->name);
            }
            return $arr;
        }
        $themeModule = ThemeModule::find()
            ->joinWith('module')
            ->where(['theme_id' => $theme->id])
            ->andWhere(['!=', 'name', 'Tab Category'])
            ->andWhere(['!=', 'name', 'Package'])
            ->all();
        $arr = [];
        $arr = [];
        foreach ($themeModule as $module) {
            $arr[$module->module_id] = Yii::t('app', $module->module->name);
        }
        return $arr;
    }

    public function getModuleName($module = null)
    {
        if ($module === null)
            $module = $this->module;
        return $this->modules[$module];
    }

    public function uploadOne($file, $collection = null, $shop = null, $name = null, $type = File::TYPE_IMAGE)

    {
        $filename = floor((microtime(true) * 100)) . "." . strtolower($file->extension);
        $folders = date('y/m/d');
        $path = $this->getBasePath() . '/' . $folders;
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $file->saveAs($path . "/" . $filename);
        $fileModel = new File();
        $fileModel->type = $type;
        $fileModel->uri = $this->uniqueUri;
        $fileModel->title = empty($name) ? (string)time() : mb_substr($name, 0, 64);
        $fileModel->name = $this->generateFileName($name) . "." . strtolower($file->extension);
        $fileModel->path = $folders . DIRECTORY_SEPARATOR . $filename;
        $fileModel->collection_id = $collection;
        // $fileModel->shop_id = $shop;
        if ($fileModel->save()) {
            return $fileModel->id;
        } else {
            return false;
        }
    }

    public function getBasePath()
    {
        return realpath(Yii::$app->basePath . '/../media');
    }

    private function generateFileName($name = null)
    {
        if (empty($name)) {
            return (string)time();
        }
        $nameWords = explode(" ", $name);
        $count = count($nameWords);
        if ($count < 5)
            return str_replace(" ", "-", $name);
        else {
            $newName = [];
            for ($i = 4; $i > 0; $i--) {
                $newName[] = $nameWords[$count - $i];
            }
            return implode("-", $newName);
        }


    }

    public function uploadAll($files, $collection, $shop = null, $name = null, $type = File::TYPE_IMAGE)
    {
        $fileSet = [];
        $folders = date('y/m/d');
        $path = $this->getBasePath() . '/' . $folders;
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        foreach ($files as $file) {
            $filename = floor((microtime(true) * 100)) . "." . strtolower($file->extension);
            $file->saveAs($path . DIRECTORY_SEPARATOR . $filename);
            $fileModel = new File();
            $fileModel->type = $type;
            $fileModel->uri = $this->uniqueUri;
            $fileModel->title = empty($name) ? (string)time() : mb_substr($name, 0, 64);
            $fileModel->name = $this->generateFileName($name) . "." . strtolower($file->extension);
            $fileModel->path = $folders . DIRECTORY_SEPARATOR . $filename;
            $fileModel->collection_id = $collection;
            //   $fileModel->shop_id = $shop;
            if ($fileModel->save()) {
                $fileSet[] = $fileModel->id;
            }
        }

        return $fileSet;
    }

    public function uploadOnePure($file, $name = null)
    {
//        $check = getimagesize($file["upload"]["tmp_name"]);
//        if ($check === false) {
//            return Yii::t('app', 'The file is not healthy');
//        }
        $extension = pathinfo($file["upload"]["name"], PATHINFO_EXTENSION);
        $filename = floor((microtime(true) * 100)) . "." . strtolower($extension);
        $folders = date('y/m/d');
        $path = $this->getBasePath() . '/' . $folders;
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        // Allow certain file formats
        $type = File::TYPE_ATTACHMENT;
        if (strpos($file["upload"]["type"], 'image') === 0) {
            $type = File::TYPE_IMAGE;
            if (!in_array($extension, ['PNG', 'JPG', 'JPEG', 'jpg', 'jpeg', 'png'])) {
                return Yii::t('app', 'Only {ext} extensions are accepted', ['ext' => 'PNG, JPG, JPEG, jpg, jpeg, png']);
            }
        } elseif (strpos($file["upload"]["type"], 'video') === 0) {
            $type = File::TYPE_VIDEO;
            if (!in_array($extension, ['mp4', 'flv', 'ogg'])) {
                return Yii::t('app', 'Only {ext} extensions are accepted', ['ext' => 'mp4, flv, ogg']);
            }
        }
        //  $file->saveAs($path . "/" . $filename);
        $fileModel = new File();
        $filepath = $path . "/" . $filename;
//        var_dump($fileModel->basePath);
//        var_dump($filename);
//        var_dump(move_uploaded_file($file["upload"]["tmp_name"], $filepath));
//        var_dump($file["upload"]["error"]);
//        exit;
        if (move_uploaded_file($file["upload"]["tmp_name"], $filepath)) {

            $fileModel->type = $type;
            $fileModel->uri = $this->getUniqueUri();
            $fileModel->title = empty($name) ? (string)time() : mb_substr($name, 0, 64);
            $fileModel->name = $this->generateFileName($name) . "." . strtolower($extension);
            $fileModel->path = $folders . "/" . $filename;
            if ($fileModel->save()) {

                return $fileModel;
            } else {

                return Yii::t('app', 'There is a problem saving the file information.');
            }
            return;
        } else {

//            var_dump($file["upload"]["error"]);
//            exit;

            echo Yii::t('app', 'There is a problem saving the file information.');
        }
    }

    public function getUniqueUri()
    {
        do {
            $uniquri = $this->generateUri();
            $model = File::find()->where(['uri' => $uniquri])->select(['id'])->limit(1)->one();
        } while ($model != null);
        return $uniquri;
    }

    public function generateUri()
    {
        $ui = uniqid();
        $a[] = substr($ui, 11, 2);
        $a[] = substr($ui, 7, 3);
        $a[] = substr($ui, 5, 1);
        $a[] = substr($ui, 10, 2);
        $uniquri = implode('', $a);
        return $uniquri;
    }

    public function getImageUriById($id, $width = null, $height = null, $resize = null)
    {
        return $this->getImageUriBase($id, $width, $height, $resize);
    }

    public function getImageUriBase($image_id = null, $width = null, $height = null, $resize = null, $absolute = false)
    {
        if (empty($image_id)) {
            if (isset($this->image_id) && !empty($this->image_id)) {
                $image_id = $this->image_id;
            } else {
                $image_id = self::NO_IMAGE_ID;
            }
        }
        $file = File::find()
            ->where(['id' => $image_id])
            ->limit(1)
            ->select(['uri', 'name', 'path'])
            ->one();
        if ($file === null) {
            $image_id = self::NO_IMAGE_ID;
            $file = File::find()
                ->where(['id' => $image_id])
                ->limit(1)
                ->select(['uri', 'name', 'path'])
                ->one();
        }
        if ($file) {
            $params['id'] = $file->uri;
        } else {
            $params['id'] = null;
        }

        if (!empty($width))
            $params['width'] = $width;
        if (!empty($height))
            $params['height'] = $height;
        if (!empty($resize))
            $params['resize'] = $resize;

        if ($file) {
            $params['name'] = $file->name;
        } else {
            $params['name'] = null;
        }


        return Url::to(array_merge(['file/image'], $params), $absolute);
    }

    public function getImageUri($width = null, $height = null, $resize = null, $absolute = false)
    {
        return $this->getImageUriBase(null, $width, $height, $resize, $absolute);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->hasAttribute('price')) {
                $this->price = str_replace(",", "", $this->price);

            }
            if ($this->hasAttribute('over_than')) {
                $this->over_than = str_replace(",", "", $this->over_than);

            }
            if ($this->hasAttribute('maximum')) {
                $this->maximum = str_replace(",", "", $this->maximum);

            }
            if ($this->isNewRecord) {
                if ($this->hasAttribute('status') && $this->status === null) {
                    $this->status = self::STATUS_PENDING;
                }
                if ($this->hasAttribute('user_id') && $this->user_id === null) {
                    $this->user_id = Yii::$app->user->id;
                }

                if ($this->hasAttribute('create_time')) {
                    $this->create_time = time();
                }
                if ($this->hasAttribute('created_at')) {
                    $this->created_at = time();
                }
            }
            return true;
        }
        return false;
    }

    public function getLinks()
    {
        return [
            self::ITEM_PRODUCT => 'محصول',
            self::ITEM_PRODUCT_CATEGORY => 'دسته بندی محصولات',
            self::ITEM_EXTERNAL_LINK => 'لینک خارجی',
            self::ITEM_BRAND => 'برند',

        ];
    }

    public function getLinkName($link = null)
    {
        if ($link === null)
            $link = $this->link;
        return $this->links[$link];

    }

    public function getStatuses()
    {
        return [
            self::STATUS_ACTIVE => Yii::t('app', 'Active'),
            self::STATUS_DISACTIVE => Yii::t('app', 'DisActive'),
        ];
    }

//    public function getStatusesSell()
//    {
//        return [
//            self::STATUS_PENDING  => Yii::t('app', 'Pending'),
//            self::STATUS_PUBLISH  => Yii::t('app', 'Publish'),
//            self::STATUS_DISABLE  => Yii::t('app', 'Disable'),
//        ];
//    }
//    public function getStatusNameSell($status = null)
//    {
//        if ($status === null){
//            $status = $this->status;
//        }
//        return $this->statusessell[$status];
//
//    }

    public function getStatusName($status = null)
    {
        if ($status === null) {
            $status = $this->status;
        }
        return $this->statuses[$status];

    }

    public function getImageTag($width = null, $height = null, $resize = null, $attributes = [])
    {
        return $this->getImageTagBase(null, $width, $height, $resize, $attributes);
    }

    public function getImageTagBase($image_id = null, $width = null, $height = null, $resize = null, $attributes = [])
    {
        if (empty($image_id)) {
            $image_id = $this->image_id;
        }
        $file = File::find()
            ->where(['id' => $image_id])
            ->limit(1)
            ->select(['uri', 'title', 'name', 'path'])
            ->one();
        if ($file === null)
            $file = File::findOne(self::NO_IMAGE_ID);

        $params['id'] = $file->uri;

        if (!empty($width))
            $params['width'] = $width;
        if (!empty($height))
            $params['height'] = $height;
        if (!empty($resize))
            $params['resize'] = $resize;
        $params['name'] = $file->name;
        $src = Url::to(array_merge(['file/image'], $params));
        return Html::img($src, array_merge(['alt' => $file->title], $attributes));
    }

    public function getTotalPrice($price, $weight = null, $wage = 0, $fee = 0, $userType = null)
    {
        $final = '';
        if (Yii::$app->params['site_name'] == 'talaforoshan') {
            $per_gram = ($weight * $price);//قیمت هرگرم طلا
            $per_wage = ($weight * ($wage));//کارمزد
            $percent = (($price * $fee) / 100);
            $per_fee = ($weight * $percent);//اجرت
            $calculate = ($per_gram + $per_fee + $per_wage);
            $calculate = round($calculate);
            switch ($userType) {
                case User::ROLE_USER :
                    $user_percent = ($calculate * self::RATE) / 100;
                    $final = $calculate + $user_percent;
                    break;
                case User::ROLE_WHOLESALER || User::ROLE_ADMIN:
                    $final = $calculate;

                    break;
            }
        } else {
            $final = $this->fee;
        }
        return (round($final));
    }

    public function getTotalPriceNoneFromat($id, $pricemodel)
    {

        $propertyPrice = PropertyValue::find()->where(['product_id' => $id])->all();
        $price = 0;
        if (count($propertyPrice) > 0) {
            foreach ($propertyPrice as $pricepro) {
                $price += $pricepro->price;

            }
        }

        $pricemodel += $price;
        return $pricemodel;

    }


    public function AllSubCategory($cat = null)
    {
        $selectedCats = Category::find()->where(['parent_id' => $cat])->all();
        if ($selectedCats) {
            foreach ($selectedCats as $selectedCat) {
                array_push($this->arrcat, $selectedCat->id);
                $this->AllSubCategory($selectedCat->id);
            }
        }
        array_push($this->arrcat, $cat);
        return array_unique($this->arrcat);
    }

}
