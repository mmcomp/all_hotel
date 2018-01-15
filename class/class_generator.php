<?php
	class class_generator
	{
		public $output = "";
		public function __construct($tableName)
		{
			$out = "<?php\n\tclass $tableName"."_class\n\t{\n";
			mysql_class::ex_sql("select * from `$tableName` where 0=1",$q);
			while($r=mysql_fetch_field($q))
				$out .= "\t\tpublic $".$r->name."=".(($r->type=="string")?"\"\"":"-1").";\n";
			$out .= "\t\tpublic function __construct(\$id=-1)\n\t\t{\n";
			$out .= "\t\t\tif((int)\$id > 0)\n";
			$out .= "\t\t\t{\n";
			$out .= "\t\t\t\tmysql_class::ex_sql(\"select * from `$tableName` where `id` = \$id\",\$q);\n";
			$out .= "\t\t\t\tif(\$r = mysql_fetch_array(\$q))\n";
			$out .= "\t\t\t\t{\n";
			$q = null;
			mysql_class::ex_sql("select * from `$tableName` where 0=1",$q);
			while($r=mysql_fetch_field($q))
				$out .= "\t\t\t\t\t\$this->".$r->name."=\$r['".$r->name."'];\n";	
			$out .= "\t\t\t\t}\n\t\t\t}\n\t\t}\n";
			$out .= "\t\tpublic function loadField(\$id,\$field)\n\t\t{\n";
			$out .= "\t\t\t\$out = FALSE;\n";
			$out .= "\t\t\tif((int)\$id > 0 && is_array(\$field) && count(\$field) > 0)\n";
			$out .= "\t\t\t{\n";
			$out .= "\t\t\t\t\$field_txt = '';\n";
			$out .= "\t\t\t\tfor(\$i = 0;\$i < count(\$field);\$i++)\n";
			$out .= "\t\t\t\t\t\$field_txt .= '`'.\$field[\$i].'`'.((\$i < count(\$field)-1)?',':'');\n";
                        $out .= "\t\t\t\tmysql_class::ex_sql(\"select \$field_txt from `$tableName` where `id` = \$id\",\$q);\n";
                        $out .= "\t\t\t\tif(\$r = mysql_fetch_array(\$q))\n";
                        $out .= "\t\t\t\t{\n";
                        $out .= "\t\t\t\t\t\$this->id=\$id;\n";
                        $out .= "\t\t\t\t\tfor(\$i = 0;\$i < count(\$field);\$i++)\n";
			$out .= "\t\t\t\t\t{\n";
			$out .= "\t\t\t\t\t\t\$this->{\$field[\$i]} = \$r[\$field[\$i]];\n";
			$out .= "\t\t\t\t\t\t\$out[\$field[\$i]] = \$r[\$field[\$i]];\n";
			$out .= "\t\t\t\t\t}\n";
                        $out .= "\t\t\t\t}\n";
                        $out .= "\t\t\t}\n";
                        $out .= "\t\t\treturn(\$out);\n";
			$out .= "\t\t}\n";
			$out .= "\t}\n?>";
			$this->output = $out;
		}
	}
?>
