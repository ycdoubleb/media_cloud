<?php

namespace frontend\modules\order_admin\utils;

use common\models\order\Order;
use common\models\order\searchs\OrderGoodsSearch;
use common\utils\DateUtil;
use common\utils\StringUtil;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use Yii;
use yii\helpers\Url;

class ExportUtils
{
    /**
     * 初始化类变量
     * @var ActionUtils 
     */
    private static $instance = null;
    
    /**
     * 获取单例
     * @return ExportUtils
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new ExportUtils();
        }
        return self::$instance;
    }
    
    /**
     * 下载支付审批申请模板
     * @param integer $id 订单ID
     */
    public function downloadTable($id)
    {
        $model = Order::findOne($id);   // Order模型
        // 订单信息
        $order_info = [
            'order_amount' => $model->order_amount,
            'upcase_order_amount' => StringUtil::toUpcaseChinese($model->order_amount),
            'order_sn' => $model->order_sn,
            'user_department' => $model->createdBy->profile->department,
            'created_by' => $model->createdBy->nickname,
            'show_link' => Url::to(['order/simple-view', 'order_sn' => $model->order_sn], true)
        ];

        $this->saveTemplate($order_info);
    }

    /**
     * 保存支付审批申请模板
     * @param array $order_info  订单信息
     */
    private function saveTemplate($order_info)
    {
        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        
        // Set document properties
        $spreadsheet->getProperties()->setCreator('Maarten Balliauw')
            ->setLastModifiedBy('Maarten Balliauw')
            ->setTitle('Office 2007 XLSX Test Document')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
            ->setKeywords('office 2007 openxml php')
            ->setCategory('Test result file');
        // 设置上下左右居中
        $allCenter = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];
        // 设置上下居中
        $verticalCenter = [
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];
        // 顶部居左
        $topLeft = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_TOP,
            ],
        ];
        // 设置边框
        $borderStyle = [
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '00000000'],
                ],
            ],
        ];
        
        // 首行标题
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A1', '素材资源内部结算审批表');
        $spreadsheet->getActiveSheet()->mergeCells('A1:E1');    //合并单元格
        $spreadsheet->getActiveSheet()->getStyle('A1:E1')->applyFromArray($allCenter);  //设置上下左右居中
        $spreadsheet->setActiveSheetIndex(0)->getRowDimension(1)->setRowHeight(80);     //设置行高
        $spreadsheet->getActiveSheet()->getStyle('A1:E1')->getFont()->setBold(true)->setName('Arial')->setSize(16);
        // 次行日期 金额
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A2', '申报日期：          年       月       日')
                ->setCellValue('D2', '金额：')->setCellValue('E2', $order_info['order_amount'] . '元');
        $spreadsheet->getActiveSheet()->mergeCells('A2:C2');    //合并单元格
        $spreadsheet->getActiveSheet()->getStyle('A2:E2')->applyFromArray($verticalCenter); //设置上下居中
        $spreadsheet->setActiveSheetIndex(0)->getRowDimension(2)->setRowHeight(20);         //设置行高
        // 第3-4行
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A3', '申报部门')->setCellValue('B3', $order_info['user_department'])
                ->setCellValue('D3', '订单编号')->setCellValueExplicit('E3', $order_info['order_sn'], DataType::TYPE_STRING);
        $spreadsheet->getActiveSheet()->mergeCells('B3:C3');    //合并单元格
        $spreadsheet->setActiveSheetIndex(0)->getRowDimension(3)->setRowHeight(40);     //设置行高
        $spreadsheet->getActiveSheet()->mergeCells('D3:D4'); 
        $spreadsheet->getActiveSheet()->mergeCells('E3:E4'); 
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A4', '申请人')->setCellValue('B4', $order_info['created_by']);
        $spreadsheet->getActiveSheet()->mergeCells('B4:C4');
        $spreadsheet->setActiveSheetIndex(0)->getRowDimension(4)->setRowHeight(40);     //设置行高
        // 第5行
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A5', '申请结算金额')->setCellValue('B5',  '(小写)：')
                ->setCellValue('C5', $order_info['order_amount'] . '元')->setCellValue('D5', '(大写)：')->setCellValue('E5', $order_info['upcase_order_amount']);
        $spreadsheet->setActiveSheetIndex(0)->getRowDimension(5)->setRowHeight(40);     //设置行高
        // 第6行
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A6', '收入部门')->setCellValue('B6', '资源中心');
        $spreadsheet->getActiveSheet()->mergeCells('B6:E6');
        $spreadsheet->setActiveSheetIndex(0)->getRowDimension(6)->setRowHeight(40);     //设置行高
        // 第7行
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A7', '素材资源用途')
            ->setCellValue('B7', "\n请填写素材资源使用的用途（用在哪个项目、课程）");
        $spreadsheet->setActiveSheetIndex(0)->getRowDimension(7)->setRowHeight(320);     //设置行高
        $spreadsheet->getActiveSheet()->mergeCells('B7:E7');
        $spreadsheet->getActiveSheet()->getStyle('B7:E7')->getFont()->getColor()->setARGB('FF999999');
        // 第8行
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A8', '订单核实地址')->setCellValue('B8', $order_info['show_link']);
        $spreadsheet->getActiveSheet()->mergeCells('B8:E8');
        $spreadsheet->setActiveSheetIndex(0)->getRowDimension(8)->setRowHeight(40);     //设置行高
        $spreadsheet->getActiveSheet()->getStyle('B8:E8')->getAlignment()->setWrapText(true);
        // 第9-11行
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A9', '审批')->setCellValue('B9', '部门负责人')
                ->setCellValue('B10', '财务')->setCellValue('B11', 'CEO');
        $spreadsheet->getActiveSheet()->mergeCells('A9:A11');
        $row = 9;
        for($row; $row <= 11; $row++){
            $spreadsheet->getActiveSheet()->mergeCells("B$row:C$row");
            $spreadsheet->getActiveSheet()->mergeCells("D$row:E$row");
            $spreadsheet->setActiveSheetIndex(0)->getRowDimension($row)->setRowHeight(26);     //设置行高
        }
        
        //设置列宽
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(10);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(18);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(12);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(26);
        
        // 设置文字排列样式
        $spreadsheet->getActiveSheet()->getStyle('A3:E11')->applyFromArray($allCenter);
        $spreadsheet->getActiveSheet()->getStyle('B7:E7')->applyFromArray($topLeft);
        
        // 设置边框
        $start = 3;
        for($start; $start <= 11; $start++){
            $spreadsheet->getActiveSheet()->getStyle("A$start")->applyFromArray($borderStyle);
            $spreadsheet->getActiveSheet()->getStyle("B$start")->applyFromArray($borderStyle);
            $spreadsheet->getActiveSheet()->getStyle("C$start")->applyFromArray($borderStyle);
            $spreadsheet->getActiveSheet()->getStyle("D$start")->applyFromArray($borderStyle);
            $spreadsheet->getActiveSheet()->getStyle("E$start")->applyFromArray($borderStyle);
        }        
        
        //设置锁定
        $spreadsheet->getActiveSheet()->getProtection()->setPassword('PhpSpreadsheet');
        $spreadsheet->getActiveSheet()->getProtection()->setSheet(true);
        $spreadsheet->getActiveSheet()->getProtection()->setSort(true);
        //$spreadsheet->getActiveSheet()->getProtection()->setInsertRows(false);
        //$spreadsheet->getActiveSheet()->getProtection()->setFormatCells(false);
        
        //设置允许修改单元格
        $spreadsheet->getActiveSheet()->getStyle('B3:B4')->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
        $spreadsheet->getActiveSheet()->getStyle('B6:B7')->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
        $spreadsheet->getActiveSheet()->getProtection()->setSelectUnlockedCells(false);
        
        // Rename worksheet
        $spreadsheet->getActiveSheet()->setTitle("支付审批申请模板");
        
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $spreadsheet->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=支付审批申请模板.xlsx');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }


    /**
     * 导出素材清单
     * @param integer $id 订单ID
     */
    public function exportMediaLists($id)
    {
        $model = Order::findOne($id);   // Order模型
        // 订单信息
        $order_info = [
            'order_sn' => $model->order_sn,
            'order_name' => $model->order_name,
            'created_by' => $model->createdBy->nickname,
            'created_at' => date('Y-m-d H:i', $model->created_at),
            'goods_num' => $model->goods_num,
            'order_amount' => $model->order_amount,
        ];
        // 商品清单
        $goodsSearch = new OrderGoodsSearch();
        $goodsDatas = $goodsSearch->searchMedia($id)->models;
        //重设素材数据里面的元素值
        foreach ($goodsDatas as &$item) {
            $item['duration'] = $item['duration'] > 0 ? DateUtil::intToTime($item['duration'], ':', true) : '';
            $item['size'] = Yii::$app->formatter->asShortSize($item['size']);
        }

        $this->saveMediaLists($order_info, $goodsDatas);
    }
    
    /**
     * 导出素材清单
     * @param array $order_info 订单信息
     * @param array $goodsDatas 商品清单
     */
    private function saveMediaLists($order_info, $goodsDatas)
    {
        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        
        // Set document properties
        $spreadsheet->getProperties()->setCreator('Maarten Balliauw')
            ->setLastModifiedBy('Maarten Balliauw')
            ->setTitle('Office 2007 XLSX Test Document')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
            ->setKeywords('office 2007 openxml php')
            ->setCategory('Test result file');
        // 设置上下左右居中
        $allCenter = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];
        // 设置上下居中
        $verticalCenter = [
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];
        // 设置边框
        $borderStyle = [
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF999999'],
                ],
            ],
        ];
       
        // 首行标题
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A1', '订单素材清单');
        $spreadsheet->getActiveSheet()->mergeCells('A1:G1');    //合并单元格
        $spreadsheet->getActiveSheet()->getStyle('A1:G1')->applyFromArray($allCenter);  //设置上下左右居中
        $spreadsheet->setActiveSheetIndex(0)->getRowDimension(1)->setRowHeight(60);     //设置行高
        $spreadsheet->getActiveSheet()->getStyle('A1:G1')->getFont()->setBold(true)->setName('Arial')->setSize(16);
        
        // 订单信息总览
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A2', '订单编号')->setCellValueExplicit('B2', $order_info['order_sn'], DataType::TYPE_STRING);
        $spreadsheet->getActiveSheet()->mergeCells('B2:G2');    //合并单元格
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A3', '订单名称')->setCellValue('B3', $order_info['order_name']);
        $spreadsheet->getActiveSheet()->mergeCells('B3:G3');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A4', '购买人')->setCellValue('B4', $order_info['created_by']);
        $spreadsheet->getActiveSheet()->mergeCells('B4:G4'); 
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A5', '下单时间')->setCellValue('B5', $order_info['created_at']);
        $spreadsheet->getActiveSheet()->mergeCells('B5:G5');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A6', '素材总数')->setCellValue('B6', $order_info['goods_num'] . '个');
        $spreadsheet->getActiveSheet()->mergeCells('B6:G6');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A7', '素材总价')->setCellValue('B7', $order_info['order_amount'] . '元');
        $spreadsheet->getActiveSheet()->mergeCells('B7:G7');
        $start = 2;
        for($start; $start <= 7; $start++){
            $spreadsheet->getActiveSheet()->getStyle("A$start")->getFont()->setBold(true);      //设置字体加粗
            $spreadsheet->getActiveSheet()->getStyle("A$start:G$start")->applyFromArray($verticalCenter);  //设置上下居中
            $spreadsheet->setActiveSheetIndex(0)->getRowDimension($start)->setRowHeight(20);    //设置行高
            $spreadsheet->getActiveSheet()->getStyle("A$start")->applyFromArray($borderStyle);  //设置边框
            $spreadsheet->getActiveSheet()->getStyle("B$start:G$start")->applyFromArray($borderStyle);
        }
        
        // 订单商品列表头
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A8', '素材编号')->setCellValue('B8', '素材名称')
                ->setCellValue('C8', '素材类型')->setCellValue('D8', '素材价格')->setCellValue('E8', '素材时长')
                ->setCellValue('F8', '素材大小')->setCellValue('G8', '素材数量');
        $spreadsheet->getActiveSheet()->getStyle('A8:G8')->applyFromArray($allCenter);
        $spreadsheet->setActiveSheetIndex(0)->getRowDimension(8)->setRowHeight(28);
        // 订单商品列表
        $startRow = 9;
        foreach ($goodsDatas as $key => $goodsData) {
            $columnIndex = 1;
            $row = $key+$startRow;
            $spreadsheet->setActiveSheetIndex(0)->getRowDimension($row)->setRowHeight(60);      //设置行高
            $spreadsheet->getActiveSheet()->getStyle("A$row:G$row")->applyFromArray($allCenter);//设置上下左右居中
            $spreadsheet->getActiveSheet()->getStyle("A$row")->applyFromArray($borderStyle);  //设置边框
            $spreadsheet->getActiveSheet()->getStyle("B$row")->applyFromArray($borderStyle);
            $spreadsheet->getActiveSheet()->getStyle("C$row")->applyFromArray($borderStyle);
            $spreadsheet->getActiveSheet()->getStyle("D$row")->applyFromArray($borderStyle);
            $spreadsheet->getActiveSheet()->getStyle("E$row")->applyFromArray($borderStyle);
            $spreadsheet->getActiveSheet()->getStyle("F$row")->applyFromArray($borderStyle);
            $spreadsheet->getActiveSheet()->getStyle("G$row")->applyFromArray($borderStyle);
            $spreadsheet->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($columnIndex, $row, $goodsData['goods_id'])
                    ->setCellValueByColumnAndRow(++$columnIndex, $row, $goodsData['media_name'])
                    ->setCellValueByColumnAndRow(++$columnIndex, $row, $goodsData['type_name'])
                    ->setCellValueByColumnAndRow(++$columnIndex, $row, $goodsData['price'])
                    ->setCellValueByColumnAndRow(++$columnIndex, $row, $goodsData['duration'])
                    ->setCellValueByColumnAndRow(++$columnIndex, $row, $goodsData['size'])
                    ->setCellValueByColumnAndRow(++$columnIndex, $row, '1');
        }
        
        //设置列宽
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(12);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(58);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(10);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(10);
        //设置字体/边框/背景颜色
        $spreadsheet->getActiveSheet()->getStyle('A8:G8')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
        $spreadsheet->getActiveSheet()->getStyle('A8:G8')->getFill()->setFillType(Fill::FILL_SOLID);
        $spreadsheet->getActiveSheet()->getStyle('A8:G8')->getFill()->getStartColor()->setARGB('808080');
        
        //设置锁定
        $spreadsheet->getActiveSheet()->getProtection()->setPassword('eematerial999');
        $spreadsheet->getActiveSheet()->getProtection()->setSheet(true);
        $spreadsheet->getActiveSheet()->getProtection()->setSort(true);
        
        // Rename worksheet
        $spreadsheet->getActiveSheet()->setTitle("订单素材清单");
        
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $spreadsheet->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=订单素材清单.xlsx');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
}