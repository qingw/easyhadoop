<?php

class Node extends Socket
{
	private $cAgentRunShell = "RunShellScript";
	
	public function ChangeHddUser($pHost, $pMountPoint)
	{
		$this->mHost = $pHost;
		$this->mCommand = $this->cAgentRunShell.":chown -R hadoop:hadoop ".$pMountPoint;
		if($this->SocketCommand())
		{
			$str = $this->mReturn;
		}
		else
		{
			$str = $lang['notConnected'];
		}
		return $str;
	}
	
	public function GetHddList($pHost)
	{
		$this->mHost = $pHost;
		$this->mCommand = $this->cAgentRunShell.":df -h | grep -v mapper | grep -w -v / | grep -v -w /boot | grep -v -w /dev | grep -v -w /lib | grep -v -w /lib64 | grep -v -w /sbin | grep -v -w /proc | grep -v -w /sys | grep -v -w /var | grep -v -w /bin | grep -v -w /root | grep -v -w /home | grep -v -w /selinux | grep -v -w /usr | awk '{print $1,$2,$3,$4,$5,$6}'";
		if($this->SocketCommand())
		{
			$str = $this->mReturn;
		}
		else
		{
			$str = $lang['notConnected'];
		}
		return $str;
	}
	
	public function HadoopStart($pHost, $pRole)
	{
		$this->mHost = $pHost;
		$this->mCommand = $this->cAgentRunShell.":sudo -u hadoop hadoop-daemon.sh start ".$pRole;
		
		$this->mForceClose = TRUE;
		if($this->SocketCommand())
		{
			$str = $this->mReturn;
		}
		else
		{
			$str = $lang['notConnected'];
		}
		return $str;
	}
	
	public function HadoopStop($pHost, $pRole)
	{
		$this->mHost = $pHost;
		$this->mCommand = $this->cAgentRunShell.":sudo -u hadoop hadoop-daemon.sh stop ".$pRole;
		
		if($this->SocketCommand())
		{
			$str = $this->mReturn;
		}
		else
		{
			$str = $lang['notConnected'];
		}
		return $str;
	}
	
	public function HadoopRestart($pHost, $pRole)
	{
		$this->mHost = $pHost;
		$this->mCommand = $this->cAgentRunShell.":sudo -u hadoop hadoop-daemon.sh stop ".$pRole;
		
		if($this->SocketCommand())
		{
			$str1 = $this->mReturn;
		}
		else
		{
			return $lang['notConnected'];
		}
		sleep(3);
		$this->mHost = $pHost;
		$this->mCommand = $this->cAgentRunShell.":sudo -u hadoop hadoop-daemon.sh start ".$pRole;
		
		$this->mForceClose = TRUE;
		if($this->SocketCommand())
		{
			$str2 = $this->mReturn;
		}
		else
		{
			return $lang['notConnected'];
		}
		$ret = $str1.$str2;
		
		return $ret;
	}
	
	public function ViewLogs($pHost, $pRole, $pHostname)
	{
		$this->mHost = $pHost;
		$this->mCommand = $this->cAgentRunShell.":tail -n 1000 /var/log/hadoop/hadoop/hadoop-hadoop-".$pRole."-".$pHostname.".log";
		if($this->SocketCommand())
		{
			$str = $this->mReturn;
		}
		else
		{
			$str = $lang['notConnected'];
		}
		
		return $str;
	}
	
	public function FormatNamenode($pHost)
	{
		$this->mHost = $pHost;
		$this->mCommand = $this->cAgentRunShell.":Y|sudo -u hadoop hadoop namenode -format";
		if($this->SocketCommand())
		{
			$str = $this->mReturn;
		}
		else
		{
			$str = $lang['notConnected'];
		}
		
		return $str;
	}
	
	public function CheckHadoopProcess($pHost, $pRole)
	{
		$this->mHost = $pHost;
		switch ($pRole)
		{
			case "namenode":
				$jps = "NameNode";
				break;
			case "jobtracker":
				$jps = "JobTracker";
				break;
			case "secondarynamenode":
				$jps = "SecondaryNameNode";
				break;
			case "datanode":
				$jps = "DataNode";
				break;
			case "tasktracker":
				$jps = "TaskTracker";
				break;
			default:
				return "Unknown Role name";
				break;
		}
		
		$command = "/usr/java/default/bin/jps | grep -w ".$jps." | awk '{print $1}'";
		$this->mCommand = $this->cAgentRunShell.":".$command;
		if($this->SocketCommand())
		{
			$str = $this->mReturn;
		}
		else
		{
			$str = $lang['notConnected'];
		}
		return $str;
	}
}

?>