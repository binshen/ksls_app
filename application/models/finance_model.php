<?php
/**
 * Created by PhpStorm.
 * User: bin.shen
 * Date: 5/31/16
 * Time: 23:00
 */

class Finance_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    public function Finance_list($page){
        // 每页显示的记录条数，默认20条
        $numPerPage = $this->input->post('numPerPage') ? $this->input->post('numPerPage') : 10;
        $pageNum = $this->input->post('pageNum') ? $this->input->post('pageNum') : $page;

        $this->db->select('count(1) as num');
        $this->db->from('fj_xiaoqu a');
        $this->db->join('fj_xiaoqu_detail b','a.id = b.xiaoqu_id','inner');
        $this->db->join('fj_xiaoqu_type c','b.type_id = c.id','inner');
        if ($this->input->post('xiaoqu')){
            $this->db->like('a.xiaoqu',trim($this->input->post('xiaoqu')));
        }
        $this->db->where(array(
            'a.flag'=>1,
            'b.pgj >'=>0
        ));
        $row = $this->db->get()->row_array();
        //总记录数
        $data['countPage'] = $row['num'];
        $data['xiaoqu'] = $this->input->post('xiaoqu') ? trim($this->input->post('xiaoqu')) : "";
        //list
        $this->db->select('a.*,b.*,c.type_name,b.id detail_id');
        $this->db->from('fj_xiaoqu a');
        $this->db->join('fj_xiaoqu_detail b','a.id = b.xiaoqu_id','inner');
        $this->db->join('fj_xiaoqu_type c','b.type_id = c.id','inner');
        if ($this->input->post('xiaoqu')){
            $this->db->like('a.xiaoqu',trim($this->input->post('xiaoqu')));
        }
        $this->db->where(array(
            'a.flag'=>1,
            'b.pgj >'=>0
        ));
        $this->db->limit($numPerPage, ($pageNum - 1) * $numPerPage );
        $this->db->order_by('a.id', 'desc');
        $data['res_list'] = $this->db->get()->result_array();
        $data['pageNum'] = $pageNum;
        $data['numPerPage'] = $numPerPage;
        return $data;
    }


    public function get_detail($id){
        $this->db->select('*');
        $this->db->from('finance a');
        //$this->db->join('fj_xiaoqu_detail b','a.id = b.xiaoqu_id','inner');
        //$this->db->join('fj_xiaoqu_type c','b.type_id = c.id','inner');
        $this->db->where(array(
            'a.id'=>$id
        ));
        $row= $this->db->get()->row_array();
        return $row;
    }

    public function create_finance_num(){

        $company_id = $this->session->userdata('login_company_id');

        $this->db->select_max('id');
        $result = $this->db->get_where('finance',
            array(
                'company_id' => $company_id,
                "DATE_FORMAT(create_date,'%Y')" => date('Y'),
            )
        )->row_array();
        $max_num = 1;
        if(!empty($result['max_num'])) {
            $max_num += $result['max_num'];
        }
        $finance_num = 'FIN' .date('Y'). str_pad($company_id, 4, "0", STR_PAD_LEFT) . str_pad($max_num, 4, "0", STR_PAD_LEFT);
        return $finance_num;
    }

    public function save_finance_1(){
        $id = $this->input->post("id");
        $data = array(
            "borrower_name" => trim($this->input->post("borrower_name")),
            "borrower_age" => $this->input->post("borrower_age"),
            "borrower_sex" => $this->input->post("borrower_sex"),
            "borrower_native" => trim($this->input->post("borrower_native")),
            "borrower_qualifications" => $this->input->post("borrower_qualifications"),
            "borrower_marriage" => $this->input->post("borrower_marriage"),
            "borrower_workADD" => trim($this->input->post("borrower_workADD")),
            "borrower_position" => trim($this->input->post("borrower_position")),
            "borrower_income" => trim($this->input->post("borrower_income")),
            "borrower_SSY" => trim($this->input->post("borrower_SSY")),
            "borrower_code" => trim($this->input->post("borrower_code")),
            "borrower_phone" => trim($this->input->post("borrower_phone")),
            //下面是配偶信息
            "spouse_name" => trim($this->input->post("spouse_name")),
            "spouse_sex" => $this->input->post("spouse_sex"),
            "spouse_native" => $this->input->post("spouse_native"),
            "spouse_age" => $this->input->post("spouse_age"),
            "spouse_qualifications" => trim($this->input->post("spouse_qualifications")),
            "spouse_workADD" => trim($this->input->post("spouse_workADD")),
            "spouse_position" => trim($this->input->post("spouse_position")),
            "spouse_income" => $this->input->post("spouse_income"),
            "spouse_SSY" => trim($this->input->post("spouse_SSY")),
            "spouse_code" => trim($this->input->post("spouse_code")),
            "spouse_phone" => trim($this->input->post("spouse_phone")),
            //亲属
            "relatives1name" => trim($this->input->post("relatives1name")),
            "relatives1tie" => trim($this->input->post("relatives1tie")),
            "relatives1age" => $this->input->post("relatives1age"),
            "relatives1position" => trim($this->input->post("relatives1position")),
            "relatives1native" => trim($this->input->post("relatives1native")),
            "relatives2name" => trim($this->input->post("relatives2name")),
            "relatives2tie" => trim($this->input->post("relatives2tie")),
            "relatives2age" => $this->input->post("relatives2age"),
            "relatives2position" => trim($this->input->post("relatives2position")),
            "relatives2native" => trim($this->input->post("relatives2native")),
            "relatives3name" => trim($this->input->post("relatives3name")),
            "relatives3tie" => trim($this->input->post("relatives3tie")),
            "relatives3age" => $this->input->post("relatives3age"),
            "relatives3position" => trim($this->input->post("relatives3position")),
            "relatives3native" => trim($this->input->post("relatives3native")),
            "relatives4name" => trim($this->input->post("relatives4name")),
            "relatives4tie" => trim($this->input->post("relatives4tie")),
            "relatives4age" => $this->input->post("relatives4age"),
            "relatives4position" => trim($this->input->post("relatives4position")),
            "relatives4native" => trim($this->input->post("relatives4native")),


        );
        $this->db->trans_start();
        if(!$id){
            $data["company_id"] = $this->session->userdata('login_company_id');
            $subsidiary_id_array = $this->session->userdata('login_subsidiary_id_array');
            $data["subsidiary_id"] = $subsidiary_id_array[0]; //在保存前已经判断用户职级,所以必然存在唯一门店编号
            $data["finance_num"] = $this->create_finance_num();
            $data["user_id"] = $this->session->userdata('login_user_id');
            $data["create_user"] = $this->session->userdata('login_user_id');
            $data["create_date"] = date('Y-m-d H:i:s');
            $data["status"] = 1;
            $this->db->insert("finance",$data);
            $id = $this->db->insert_id();

        }else{
            $this->db->where('id',$id)->update("finance",$data);
        }

        $this->db->trans_complete();//------结束事务
        if ($this->db->trans_status() === FALSE) {
            return -1;
        } else {
            return $id;
        }

    }

    public function save_finance_2(){

        $data = array(
            "borrower_hasP" => $this->input->post('borrower_hasP'),
            "property_community" => trim($this->input->post("property_community")),
            "property_num" => trim($this->input->post("property_num")),
            "property_estates" => trim($this->input->post("property_estates")),
            "property_area" => trim($this->input->post("property_area")),
            "property_price" => trim($this->input->post("property_price")),
            "property_owner" => trim($this->input->post("property_owner")),
            "property_SF" => trim($this->input->post("property_SF")),
            "property_YG" => trim($this->input->post("property_YG")),
            "property_AJ" => trim($this->input->post("property_AJ")),

            "borrowing_amount" => $this->input->post('borrowing_amount'),
            "repayment" => trim($this->input->post("repayment")),
            "repayment_methods" => trim($this->input->post("repayment_methods")),
            "explain_XYK" => $this->input->post("explain_XYK",true),
            "explain_AJ" => $this->input->post("explain_AJ",true),
            "explain_ZY" => $this->input->post("explain_ZY",true),
            "explain_SYBX" => $this->input->post("explain_SYBX",true),
            "explain_SFZC" => $this->input->post("explain_SFZC",true)


        );
        $this->db->trans_start();

        $this->db->where('id',$this->input->post("id"))->update("finance",$data);

        $this->db->trans_complete();//------结束事务
        if ($this->db->trans_status() === FALSE) {
            return -1;
        } else {
            return 1;
        }


    }

    //save_finance_tj 和 save_finnace_3 是一样的,目的是为了以后在提交和从第三页返回这两个动作区分开来
    public function save_finance_tj(){

        $data = array(
            "status" => 2,
            "tijiao_date" => date('Y-m-d H:i:s'),
        );
        $this->db->trans_start();

        $this->db->where('id',$this->input->post("id"))->update("finance",$data);

        $this->db->trans_complete();//------结束事务
        if ($this->db->trans_status() === FALSE) {
            return -1;
        } else {
            return 1;
        }
    }

    public function save_finance_3(){

        $data = array(
            "borrower_img_SFZ1" => $this->input->post('borrower_img_SFZ1'),
            "borrower_img_SFZ2" => $this->input->post('borrower_img_SFZ2'),
            "spouse_img_SFZ1" => $this->input->post('spouse_img_SFZ1'),
            "spouse_img_SFZ2" => $this->input->post('spouse_img_SFZ2'),
            "img_JHZ1" => $this->input->post('img_JHZ1'),
            "img_JHZ2" => $this->input->post('img_JHZ2'),
            "img_SBZ" => $this->input->post('img_SBZ'),
            "img_BDC" => $this->input->post('img_BDC'),
            "img_ZXBG" => $this->input->post('img_ZXBG'),
            "img_YHLS" => $this->input->post('img_YHLS'),
        );
        $this->db->trans_start();

        $this->db->where('id',$this->input->post("id"))->update("finance",$data);

        $this->db->trans_complete();//------结束事务
        if ($this->db->trans_status() === FALSE) {
            return -1;
        } else {
            return 1;
        }
    }

    public function save_power($id){
        $data = $this->db->from('finance')->where('id',$id)->get()->row_array();
        if(!$data)
            return -1;
        if($data['user_id'] == $this->session->userdata('login_user_id')){
            if($data['status'] == 1){
                return 1;
            }else{
                return -3;
            }

        }else{
            return -2;
        }
    }

}