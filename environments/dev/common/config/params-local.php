<?php
return [
    /* ffmpeg配置 */
    'ffmpeg' => [
        'ffmpeg.binaries' => 'D:/Program Files/ffmpeg/bin/ffmpeg.exe',
        'ffprobe.binaries' => 'D:/Program Files/ffmpeg/bin/ffprobe.exe',
    ],
    /* 测试机 ffmpeg配置 */
    /*
    'ffmpeg' => [
        'ffmpeg.binaries' => '/usr/bin/ffmpeg',
        'ffprobe.binaries' => '/usr/bin/ffprobe',
    ],*/
    /* 阿里云OSS配置 */
    
    'aliyun' => [
        'accessKeyId' => 'LTAIM0fcBM6L6mTa',
        'accessKeySecret' => '2fSyGRwesxyP4X2flUF35n7brgxlEf',
        'oss' => [
            'bucket-input' => 'studying8',
            'bucket-output' => 'studying8',
            'host-input' => 'studying8.oss-cn-shenzhen.aliyuncs.com',              
            'host-output' => 'file.studying8.com',            
            'host-input-internal' => 'studying8.oss-cn-shenzhen.aliyuncs.com',  //测试使用外网地址
            'host-output-internal' => 'studying8.oss-cn-shenzhen.aliyuncs.com', //测试使用外网地址
            'endPoint' => 'oss-cn-shenzhen.aliyuncs.com',
            'endPoint-internal' => 'oss-cn-shenzhen.aliyuncs.com',              //测试使用外网地址
        ],
        'mts' => [
            'region_id' => 'cn-shenzhen',                               //区域
            'pipeline_id' => 'd51a05c98fca4984923e7fb6f5536a45',        //管道ID
            'pipeline_name' => 'new-pipeline',                          //管道名称
            'oss_location' => 'oss-cn-shenzhen',                        //作业输入，华南1
            'template_id_ld' => 'ccc005515a26e19823ff91fd55fe16a6',     //流畅模板ID
            'template_id_sd' => '4e6fda4f21c6b2b4e9affa91e8030c6e',     //标清模板ID
            'template_id_hd' => 'ebf8396f0260c5e8d3563c58b2c4b2cb',     //高清模板ID
            'template_id_fd' => 'ec4ec87b7382c154f6c61b0973bf67ef',     //超畅模板ID
            'water_mark_template_id' => '15b2d6094e8448c493cd113a90e330e3',     //水印模板ID 默认右上
            'topic_name' => 'studying8-transcode',                      //消息通道名
        ]
    ],
    /* wskeee 阿里云OSS配置 
    
    'aliyun' => [
        'accessKeyId' => 'LTAIcHRbE9LWXKL7',
        'accessKeySecret' => 'OVwOCkxVghIrQPrCyvAA4UREPoXVqD',
        'oss' => [
            'bucket-input' => 'wskeee-studying8',
            'bucket-output' => 'wskeee-studying8',
            'host-input' => 'wskeee-studying8.oss-cn-shenzhen.aliyuncs.com',              
            'host-output' => 'wskeee-studying8.oss-cn-shenzhen.aliyuncs.com',            
            'host-input-internal' => 'wskeee-studying8.oss-cn-shenzhen.aliyuncs.com',  //测试使用外网地址
            'host-output-internal' => 'wskeee-studying8.oss-cn-shenzhen.aliyuncs.com', //测试使用外网地址
            'endPoint' => 'oss-cn-shenzhen.aliyuncs.com',
            'endPoint-internal' => 'oss-cn-shenzhen.aliyuncs.com',              //测试使用外网地址
        ],
        'mts' => [
            'region_id' => 'cn-shenzhen',                               //区域
            'pipeline_id' => 'b1fe3fe97b6b42e499cac7969161f5d5',        //管道ID
            'pipeline_name' => 'mts-service-pipeline',                          //管道名称
            'oss_location' => 'oss-cn-shenzhen',                        //作业输入，华南1
            'template_id_ld' => '015f0c886c3b468f8908fb05784b760d',     //流畅模板ID
            'template_id_sd' => '85136b20ecea44d1ae980c43d93a9d6e',     //标清模板ID
            'template_id_hd' => '719d5f1c54fa4f5d8042eaf0e20e46ec',     //高清模板ID
            'template_id_fd' => 'd97820623858459fbf7a14913e00039b',     //超畅模板ID
            'water_mark_template_id' => '9b8da9c8bd234fabae1d88d1581a8435',     //水印模板ID 默认右上
            'topic_name' => 'studying8-transcode',                      //消息通道名
        ]
    ],*/
];
