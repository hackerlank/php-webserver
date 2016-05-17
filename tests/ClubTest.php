<?php
class ClubTest extends tests_base
{
    //获取俱乐部信息
    public function testGetClubInfo()
    {        
        $clubCtrl = $this->getCtrlObject('cls_ctrl_interface_club');
        $clubInfo = $clubCtrl->getClubInfo();        
        // Assert
        $this->assertEquals($this->club_id, $clubInfo['club_id']);
    }


    //加游戏币
    public function testAddMoney()
    {  
        $clubServer =  $this->serviceLocator->getService ( "club" );
        $clubInfo = $clubServer->getClub($this->club_id);

        $old_money = $clubInfo['money'];
        $awardInfo = array('money'=>10);
        $clubServer->upgradeClub('cmroad',$this->club_id,$awardInfo);  

        $clubInfo = $clubInfo = $clubServer->getClub($this->club_id);
        // Assert
        $this->assertEquals($old_money + 10,$clubInfo['money']);
    }


    //俱乐部经验
    public function testAddClubExp()
    {  
        $clubServer =  $this->serviceLocator->getService ( "club" );
        $clubInfo = $clubServer->getClub($this->club_id);
        $old_club_level = $clubInfo['club_level'];
        $old_club_level_exp = $clubInfo['club_level_exp'];

        $clubServer->addClubExp($this->club_id,'cmroad',10); 

        $clubInfo = $clubInfo = $clubServer->getClub($this->club_id);
        $club_level = $clubInfo['club_level'];
        $club_level_exp = $clubInfo['club_level_exp'];

        if($old_club_level == $club_level){
            $this->assertEquals($old_club_level_exp + 10,$club_level_exp);
        } 
    }

    //加金币
    public function testAddClubGold()
    {  
        $clubServer =  $this->serviceLocator->getService ( "club" );
        $clubInfo = $clubServer->getClub($this->club_id);

        $old_gold = $clubInfo['gold'];
        $awardInfo = array('gold'=>10);
        $clubServer->upgradeClub('cmroad',$this->club_id,$awardInfo);  

        $clubInfo = $clubInfo = $clubServer->getClub($this->club_id);
        // Assert
        $this->assertEquals($old_gold + 10,$clubInfo['gold']);     
    }

     //加天梯积分
    public function testAddClubLadder()
    {  
        $clubServer =  $this->serviceLocator->getService ( "club" );
        $clubInfo = $clubServer->getClub($this->club_id);

        $old_ladder_score = $clubInfo['ladder_score'];
        $awardInfo = array('ladder_score'=>10);
        $clubServer->upgradeClub('cmroad',$this->club_id,$awardInfo);  

        $clubInfo = $clubInfo = $clubServer->getClub($this->club_id);
        // Assert
        $this->assertEquals($old_ladder_score + 10,$clubInfo['ladder_score']);     
    }

     //更换队徽
    public function updateClubIcon()
    {
        $icon = (string)mt_rand(1,17);      
        $clubCtrl = $this->getCtrlObject('cls_ctrl_interface_club',array('icon'=> $icon));
        $ret = $clubCtrl->updateClubIcon();
        $this->assertEquals($ret['updateCache']['clubInfo']['club_icon'], $icon);
        
    }
}