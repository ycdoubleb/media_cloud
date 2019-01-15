<?php

namespace frontend\modules\order_admin\utils;

use common\models\order\Order;
use common\models\order\searchs\OrderGoodsSearch;
use common\utils\DateUtil;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Yii;

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
            'order_sn' => $model->order_sn,
            'show_link' => \yii\helpers\Url::to(['order/simple-view', 'order_sn' => $model->order_sn], true)
        ];
//        var_dump($order_info);exit;
        $this->saveTemplate($order_info);
    }

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
        // 设置上下居中
        $allCenter = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];
        // 底部居左
        $bottomDefalut = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_BOTTOM,
            ],
        ];
        // 边框
        $borderStyle = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00000000'],
                ],
            ],
        ];
        
        // 首行标题
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A1', '媒体资源内部结算审批表');
        $spreadsheet->getActiveSheet()->mergeCells('A1:E1');    //合并单元格
        $spreadsheet->getActiveSheet()->getStyle('A1:E1')->applyFromArray($allCenter); //设置上下居中
        $spreadsheet->setActiveSheetIndex(0)->getRowDimension(1)->setRowHeight(80);     //设置行高
        $spreadsheet->getActiveSheet()->getStyle('A1:E1')->getFont()->setBold(true)->setName('Arial')->setSize(16);
        // 次行日期 金额
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A2', '申报日期：          年       月       日')
                ->setCellValue('D2', '金额：')->setCellValue('E2', $order_info['order_amount'] . '元');
        $spreadsheet->getActiveSheet()->mergeCells('A2:C2');    //合并单元格
        
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A3', '申报部门')->setCellValue('D3', '订单编号')->setCellValue('E3', $order_info['order_sn']);
        $spreadsheet->getActiveSheet()->mergeCells('B3:C3');    //合并单元格
        $spreadsheet->setActiveSheetIndex(0)->getRowDimension(3)->setRowHeight(40);     //设置行高
        $spreadsheet->getActiveSheet()->mergeCells('D3:D4'); 
        $spreadsheet->getActiveSheet()->mergeCells('E3:E4'); 
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A4', '申报人');
        $spreadsheet->getActiveSheet()->mergeCells('B4:C4');
        $spreadsheet->setActiveSheetIndex(0)->getRowDimension(4)->setRowHeight(40);     //设置行高
        
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A5', '申请结算金额')->setCellValue('B5', $order_info['order_amount'] . '元')
                ->setCellValue('C5', '(小写)：')->setCellValue('D5', '(大写)：');
        $spreadsheet->setActiveSheetIndex(0)->getRowDimension(5)->setRowHeight(40);     //设置行高
        
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A6', '收入部门');
        $spreadsheet->getActiveSheet()->mergeCells('B6:E6');
        $spreadsheet->setActiveSheetIndex(0)->getRowDimension(6)->setRowHeight(40);     //设置行高
        
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A7', '媒体资源用途')
            ->setCellValue('B7', "1、 媒体资源数量（媒体资源明细见附件  订单媒体清单）\n2、 媒体资源使用的用途（用在哪个项目、课程）附件(资源核实地址)：".$order_info['show_link']);
        $spreadsheet->setActiveSheetIndex(0)->getRowDimension(7)->setRowHeight(240);     //设置行高
        $spreadsheet->getActiveSheet()->mergeCells('B7:E7');
        $spreadsheet->getActiveSheet()->getStyle('B7:E7')
            ->getFont()->getColor()->setARGB('FF999999');
        
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A8', '审批')->setCellValue('B8', '部门负责人')
                ->setCellValue('B9', '财务')->setCellValue('B10', 'CEO');
        $spreadsheet->getActiveSheet()->mergeCells('A8:A10');
        $spreadsheet->getActiveSheet()->mergeCells('B8:C8');
        $spreadsheet->getActiveSheet()->mergeCells('B9:C9');
        $spreadsheet->getActiveSheet()->mergeCells('B10:C10');
        $spreadsheet->getActiveSheet()->mergeCells('D8:E8');
        $spreadsheet->getActiveSheet()->mergeCells('D9:E9');
        $spreadsheet->getActiveSheet()->mergeCells('D10:E10');
        $spreadsheet->setActiveSheetIndex(0)->getRowDimension(8)->setRowHeight(26);     //设置行高
        $spreadsheet->setActiveSheetIndex(0)->getRowDimension(9)->setRowHeight(26);     //设置行高
        $spreadsheet->setActiveSheetIndex(0)->getRowDimension(10)->setRowHeight(26);     //设置行高
        
        //设置列宽
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(10);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(18);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(12);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(26);
        
        // 设置文字排列样式
        $spreadsheet->getActiveSheet()->getStyle('A3:E10')->applyFromArray($allCenter);
        $spreadsheet->getActiveSheet()->getStyle('B7:E7')->applyFromArray($bottomDefalut);
        $spreadsheet->getActiveSheet()->getStyle('A3:E10')->applyFromArray($borderStyle);
        
        
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
     * 导出媒体清单
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
        //重设媒体数据里面的元素值
        foreach ($goodsDatas as &$item) {
            $item['duration'] = $item['duration'] > 0 ? DateUtil::intToTime($item['duration'], ':', true) : null;
            $item['size'] = Yii::$app->formatter->asShortSize($item['size']);
        }

        $this->saveMediaLists($order_info, $goodsDatas);
    }
    
    /**
     * 导出媒体清单
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
        // 设置上下居中
        $styleArray = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];
       
        // 首行标题
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A1', '订单媒体清单');
        $spreadsheet->getActiveSheet()->mergeCells('A1:G1');    //合并单元格
        $spreadsheet->getActiveSheet()->getStyle('A1:G1')->applyFromArray($styleArray); //设置上下居中
        $spreadsheet->setActiveSheetIndex(0)->getRowDimension(1)->setRowHeight(60);     //设置行高
        $spreadsheet->getActiveSheet()->getStyle('A1:G1')->getFont()->setBold(true)->setName('Arial')->setSize(16);
        
        // 订单信息总览
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A2', '订单编号：')->setCellValue('B2', $order_info['order_sn']);
        $spreadsheet->getActiveSheet()->mergeCells('B2:G2');    //合并单元格
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A3', '订单名称：')->setCellValue('B3', $order_info['order_name']);
        $spreadsheet->getActiveSheet()->mergeCells('B3:G3');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A4', '购买人：')->setCellValue('B4', $order_info['created_by']);
        $spreadsheet->getActiveSheet()->mergeCells('B4:G4'); 
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A5', '下单时间：')->setCellValue('B5', $order_info['created_at']);
        $spreadsheet->getActiveSheet()->mergeCells('B5:G5');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A6', '媒体总数：')->setCellValue('B6', $order_info['goods_num'] . '个');
        $spreadsheet->getActiveSheet()->mergeCells('B6:G6');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A7', '媒体总价：')->setCellValue('B7', $order_info['order_amount'] . '元');
        $spreadsheet->getActiveSheet()->mergeCells('B7:G7');
        
        // 订单商品列表头
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A8', '媒体编号')->setCellValue('B8', '媒体名称')
                ->setCellValue('C8', '媒体类型')->setCellValue('D8', '媒体价格')->setCellValue('E8', '媒体时长')
                ->setCellValue('F8', '媒体大小')->setCellValue('G8', '媒体数量');
        $spreadsheet->getActiveSheet()->getStyle('A8:G8')->applyFromArray($styleArray);
        $spreadsheet->setActiveSheetIndex(0)->getRowDimension(8)->setRowHeight(28);
        // 订单商品列表
        $startRow = 9;
        foreach ($goodsDatas as $key => $goodsData) {
            $columnIndex = 1;
            $row = $key+$startRow;
            $spreadsheet->setActiveSheetIndex(0)->getRowDimension($row)->setRowHeight(60);    //设置行高
            $spreadsheet->getActiveSheet()->getStyle("A$row:G$row")->applyFromArray($styleArray);    //设置上下居中
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
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(18);
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
        
        // Rename worksheet
        $spreadsheet->getActiveSheet()->setTitle("订单媒体清单");
        
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $spreadsheet->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=订单媒体清单.xlsx');
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