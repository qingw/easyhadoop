<?php
include_once "config.inc.php";

include_once "templates/header.html";
include_once "templates/node_manager_sidebar.html";

$mysql = new Mysql();
$socket = new Socket;

##默认页面
if(!@$_GET['action'])
{
	echo '<div class=span10>';
	
	$sql = "select * from ehm_hosts order by create_time desc";
	$mysql->Query($sql);
	echo '<table class="table table-striped">';
	echo '<thead>
                <tr>
                  <th>#</th>
                  <th>'.$lang['hostname'].'</th>
                  <th>'.$lang['ipAddr'].'</th>
                  <th>'.$lang['nodeRole'].'</th>
                  <th>'.$lang['createTime'].'</th>
                </tr>
                </thead>
                <tbody>';
	$i = 1;
	while($arr = $mysql->FetchArray())
	{
		echo '<tr>
                  <td>'.$i.'</td>
                  <td>'.$arr['hostname'].'</td>
                  <td>'.$arr['ip'].'</td>
                  <td>'.$arr['role'].'</td>
                  <td>'.$arr['create_time'].'</td>
                </tr>';
		$i++;
	}
	echo '</tbody></table>';
	echo '</div>';
}

##添加节点到数据库
elseif ($_GET['action'] == "AddNode")
{
	if(!$_POST['ip'] && !$_POST['hostname'] && !$_POST['role'])
	{
		echo '<div class="span10">
            <h1>'.$lang['addNode'].'</h1>';
            
        echo '<div class="alert alert-error">';
		echo $lang['addNodeTips'];
		echo '</div>';
			
        include_once 'templates/add_node_form.html';
		
		echo '</div>';
	}
	else
	{
		echo '<div class=span10>';
		$hostname = $_POST['hostname'];
		$ipaddr = $_POST['ipaddr'];
		$role = strtolower(join(',',$_POST['role']));
		$sql = "insert ehm_hosts set hostname = '".$hostname."', ip = '".$ipaddr."', role = '".$role."', create_time=current_timestamp()";
		$mysql->Query($sql);
		echo '</div>';
		echo "<script>alert('".$lang['nodeAdded']."'); this.location='NodeManager.php';</script>";
	}
}
#从数据库中删除节点
elseif ($_GET['action'] == "RemoveNode")
{
	if(!$_GET['nodeid'])
	{
		echo '<div class=span10>';
		echo '<h1>'.$lang['removeNode'].'</h1>';
		$sql = "select * from ehm_hosts order by create_time desc";
		$mysql->Query($sql);
		
		echo '<div class="alert alert-error">';
		echo $lang['addNodeTips'];
		echo '</div>';
		
		echo '<table class="table table-striped">';
		echo '<thead>
                	<tr>
                  	<th>#</th>
                  	<th>'.$lang['hostname'].'</th>
                  	<th>'.$lang['ipAddr'].'</th>
                  	<th>'.$lang['nodeRole'].'</th>
                  	<th>'.$lang['createTime'].'</th>
                  	<th>'.$lang['removeNode'].'</th>
                	</tr>
                	</thead>
                	<tbody>';
		$i = 1;
		while($arr = $mysql->FetchArray())
		{
			echo '<tr>
                  	<td>'.$i.'</td>
                  	<td>'.$arr['hostname'].'</td>
                  	<td>'.$arr['ip'].'</td>
                  	<td>'.$arr['role'].'</td>
                  	<td>'.$arr['create_time'].'</td>
                  	<td><a class="btn btn-danger" onclick="javascript:realconfirm(\''.$lang['removeConfirm'].'\', \'NodeManager.php?action=RemoveNode&nodeid='.$arr['host_id'].'&ip='.$arr['ip'].'\');return false;" href="#"><i class=icon-remove></i>'.$lang['removeNode'].'</a></td>
                	</tr>';
			$i++;
		}
		echo '</tbody></table>';
		echo '</div>';
	}
	else
	{
		$host_id = $_GET['nodeid'];
		$ip = $_GET['ip'];
		$sql = "delete from ehm_hosts where host_id='".$host_id."'";
		$mysql->Query($sql);
		$sql = "delete from ehm_host_settings where ip='".$ip."'";
		$mysql->Query($sql);
		echo "<script>alert('".$lang['nodeRemoved']."'); this.location='NodeManager.php?action=RemoveNode';</script>";
	}
}

elseif ($_GET['action'] == "ModifyNode")
{
	if(!$_GET['ip'])
	{
		echo '<div class=span10>';
		echo '<h1>'.$lang['modifyNode'].'</h1>';
		
		echo '<div class="alert alert-error">';
		echo $lang['modifyNodeTips'];
		echo '</div>';
		
		$sql = "select * from ehm_hosts order by create_time desc";
		$mysql->Query($sql);
		echo '<table class="table table-striped">';
		echo '<thead>
                	<tr>
                  	<th>#</th>
                  	<th>'.$lang['hostname'].'</th>
                  	<th>'.$lang['ipAddr'].'</th>
                  	<th>'.$lang['nodeRole'].'</th>
                  	<th>'.$lang['createTime'].'</th>
                  	<th>'.$lang['action'].'</th>
                	</tr>
                	</thead>
                	<tbody>';
		$i = 1;
		while($arr = $mysql->FetchArray())
		{
			echo '<tr>
                  	<td>'.$i.'</td>
                  	<td>'.$arr['hostname'].'</td>
                  	<td>'.$arr['ip'].'</td>
                  	<td>'.$arr['role'].'</td>
                  	<td>'.$arr['create_time'].'</td>
                  	<td><a class="btn btn-warning" href=NodeManager.php?action=ModifyNode&nodeid='.$arr['host_id'].'&ip='.$arr['ip'].'><i class=icon-pencil></i>'.$lang['modifyNode'].'</a></td>
                	</tr>';
			$i++;
		}
		echo '</tbody></table>';
		echo '</div>';
	}
	else
	{
		$ip = $_GET['ip'];
		$sql = "select * from ehm_hosts where ip='".$ip."'";
		$mysql->Query($sql);
		$arr = $mysql->FetchArray();
		if(!$_POST['ip'] && !$_POST['hostname'] && !$_POST['role'])
		{
			echo '<div class="span10">
            	<h1>'.$lang['modifyNode'].'</h1>';
			include_once "templates/edit_node_form.html";
			echo '</div>';
		}
		else
		{
			$hostname = $_POST['hostname'];
			$ip = $_POST['ipaddr'];
			$role = $_POST['role'];
			$host_id = $_POST['host_id'];
			$sql = "update ehm_hosts set hostname='".$hostname."', ip='".$ip."', role='".$role."' where host_id=".$host_id;
			$mysql->Query($sql);
			echo "<script>alert('".$lang['nodeModified']."'); this.location='NodeManager.php?action=ModifyNode';</script>";
		}
	}
}

##连通性测试
elseif($_GET['action'] == "PingNode")
{
	if(!$_GET['nodeid'])
	{
		echo '<div class=span10>';
	
		$sql = "select * from ehm_hosts order by create_time desc";
		$mysql->Query($sql);
		echo '<table class="table table-striped">';
		echo '<thead>
                	<tr>
                  	<th>#</th>
                  	<th>'.$lang['hostname'].'</th>
                  	<th>'.$lang['ipAddr'].'</th>
                  	<th>'.$lang['nodeRole'].'</th>
                  	<th>'.$lang['createTime'].'</th>
                  	<th>'.$lang['action'].'</th>
                	</tr>
                	</thead>
                	<tbody>';
		$i = 1;
		while($arr = $mysql->FetchArray())
		{
			echo '<tr>
                  	<td>'.$i.'</td>
                  	<td>'.$arr['hostname'].'</td>
                  	<td>'.$arr['ip'].'</td>
                  	<td>'.$arr['role'].'</td>
                  	<td>'.$arr['create_time'].'</td>
                  	<td><a class="btn btn-info" href=NodeManager.php?action=PingNode&nodeid='.$arr['host_id'].'><i class=icon-play></i>'.$lang['pingNode'].'</a></td>
                	</tr>';
			$i++;
		}
		echo '</tbody></table>';
		echo '</div>';
	}
	else
	{
		$sql = "select ip from ehm_hosts where host_id='".$_GET['nodeid']."'";
		$mysql->Query($sql);
		$arr = $mysql->FetchArray();
		if($socket->SocketConnectTest($arr['ip']))
		{
			echo "<script>alert('".$lang['connected']."'); this.location='NodeManager.php?action=PingNode';</script>";
		}
		else
		{
			echo "<script>alert('".$lang['notConnected']."'); this.location='NodeManager.php?action=PingNode';</script>";
		}
	}
}

include_once "templates/footer.html";
?>