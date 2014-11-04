<?php


/**
 * 表单排序类
 * @category   ORG
 * @package  ORG
 * @subpackage  Util
 * @author    Aoki
 * @version   $Id$
 */

class Sort extends Think{
    public $id;         //当前 组/分类 的ID
    public $field;      //排序的字段
    public $order;      //排序顺序

    /**
     * Construct function
     * @access public
     */
    public function __construct($field='id',$order='desc',$id=0) {
        $this->field = $field?($field!=''?$field:'id'):'id';
        $this->order = $order?($order!=''?$order:'desc'):'desc';
        $this->id    = $id?intval($id):0;
    }

    /**
     * 排序条件初始化
     * @access public
     * @param $field string
     * @param $order string
     * @param $id    int
     * @return void
     */
    public function setSort($field='id',$order='desc',$id=0) {
        $this->field = $field?($field!=''?$field:'id'):'id';
        $this->order = $order?($order!=''?$order:'desc'):'desc';
        $this->id    = $id?intval($id):0;
    }

    /**
     * 排序条件初始化
     * @access public
     * @return string
     */
    public function output() {
        return $this->field.' '.$this->order;
    }

    /**
     * 反转排序顺序
     * @access public
     * @return string
     */
    public function reverseOrder() {
        $this->order = $this->order=='desc'?'asc':'desc';
    }

    /**
     * 返回排序数组，用于模板输出
     * @access public
     * @return array
     */
    public function getSortArray($id=0) {
        $this->reverseOrder();
        return array(
            'id'=>$id,
            'field'=>$this->field,
            'order'=>$this->order
        );
    }

}

?>