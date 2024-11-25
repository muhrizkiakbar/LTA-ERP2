<html>
<head>
	<!-- Fonts  -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
	<!-- Bootstrap core CSS -->
	<link rel="stylesheet" href="<?php echo base_url();?>assets/css/print/bootstrap.css" />
	<!-- Base Styling  -->
	<link rel="stylesheet" href="<?php echo base_url();?>assets/css/print/core.css" />
	<style>
	  *{	
	  	font-family: sans-serif;
			color: #000;
	  }

	  table{
	    font-size: 14px;
	  }


	  .spacingtd {
	    padding: 0px 0px 0px 10px !important;
	    margin: 0 !important;
	  }

	  .spacing tr td {
	    padding: 0 10px 0 0 !important;
	    margin: 0 !important;
	  }

		.page {
			width: 21cm;
			min-height: 29.7cm;
			padding: 0.75cm;
			margin: 0.75cm auto;
			background: white;
		}

		@page {
        size: F4;
        margin: 0.5cm;
		}

		@media print {
			.page {
				margin: 0;
				border: initial;
				border-radius: initial;
				width: initial;
				min-height: initial;
				box-shadow: initial;
				background: initial;
				page-break-after: always;
			}
		}

		@media print and (color) {
      table { page-break-after:auto }
      tr    { page-break-inside:avoid; page-break-after:auto }
      td    { page-break-inside:avoid; page-break-after:auto }
      thead { display:table-header-group; margin-top: 10px; }
      tfoot { display:table-footer-group }
		  * {
		      -webkit-print-color-adjust: exact;
		      print-color-adjust: exact;
		  }
		}
 	</style>
</head>
<body style="background: #fff;">
	<div class="page">
		<div class="row">
			<div class="col-md-12">
				<img src="<?php echo base_url();?>assets/images/logo-echs1.png" width="110" style="position: absolute; width: 130px;">
				<div style="margin-top: -5px; margin-bottom: -15px;">
					<center>
						<p style="font-size: 20px; font-weight: bold; margin-bottom: 2px;">
							ESA CIPTA HARAPAN <?php echo gradeket($grade);?> 
						</p>
						<p style="font-size: 14px; font-weight: 400;">
							<?php echo gradenss($grade);?><br>
							<?php echo gradealamat($grade);?><br>
              Website : www.esaciptaharapan.sch.id Email : admission@esaciptaharapan.sch.id
						</p>
					</center>
				</div>
				<hr style="border: 1px solid #000; margin-bottom: 0px;">
			</div>
		</div>
		<div class="row">
      <div class="col-md-12">
      	<div>
					<center>
						<p style="font-size: 22px; font-weight: bold; margin-bottom: 2px;">
							REPORT CARD
						</p>
					</center>
				</div>
        <table>
          <tr>
            <td width="450px">
              <table>
                <tr>
                  <td height="20px">NAME</td>
                  <td width="50px"><center>:</center></td>
                  <td><?php echo getStudentName($studentid);?></td>
                </tr>
                <tr>
                  <td height="20px">NIS / NISN</td>
                  <td class="text-center">:</td>
                  <td><?php echo $studentid;?></td>
                </tr>
                <tr>
                  <td height="20px">GRADE</td>
                  <td class="text-center">:</td>
                  <td><?php echo getGradeTitle($grade);?></td>
                </tr>
              </table>
            </td>
            <td valign="top">
            	<table>
                <tr>
                  <td height="20px">SEMESTER</td>
                  <td width="50px"><center>:</center></td>
                  <td><?php echo $smt;?></td>
                </tr>
                <tr>
                  <td height="20px">ACADEMIC YEAR</td>
                  <td class="text-center">:</td>
                  <td><?php echo academic_year($academic_year);?></td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <h5 style="margin-top:20px;"><b>A. CHARACTER</b></h5>
        <table border="1" width="100%">
          <thead>
            <tr>
              <th class="text-center" width="30px">NO</th>
              <th class="text-center" width="140px">ASPECT</th>
              <th class="text-center">DESCRIPTION</th>
            </tr>
            <?php foreach ($character->result() as $character) { ?>
            <tr>
              <td class="text-center">1</td>
              <td style="padding-left: 5px;">Spiritual</td>
              <td style="padding-left: 5px;"><?php echo $character->spiritual;?></td>
            </tr>
            <tr>
              <td class="text-center">2</td>
              <td style="padding-left: 5px;">Sosial</td>
              <td style="padding-left: 5px;"><?php echo $character->sosial;?></td>
            </tr>
            <?php } ?>
          </thead>
        </table>
        <h5 style="margin-top:20px;"><b>B. COGNITIVE & SKILL</b></h5>
        <table border="1" width="100%">
          <col style="width:5%">
          <col style="width:23%">
          <thead>
            <tr>
              <th class="text-center" width="30px" rowspan="2">NO</th>
              <th class="text-center" rowspan="2">SUBJECT</th>
              <th class="text-center" colspan="3">COGNITIVE</th>
              <th class="text-center" colspan="3">SKILL</th>
            </tr>
            <tr>
              <th class="text-center" width="50px">SCORE</th>
              <th class="text-center" width="50px">GRADE</th>
              <th class="text-center" width="50px">REMARK</th>
              <th class="text-center" width="50px">SCORE</th>
              <th class="text-center" width="50px">GRADE</th>
              <th class="text-center" width="50px">REMARK</th>
            </tr>
          </thead>
          <tbody>
            <?php $no=1; foreach ($raport as $key => $group) { ?>
            <tr>
              <td colspan="8">
                <strong style="margin-left: 5px;"><?php echo $key;?></strong>
              </td>
            </tr>
              <?php foreach ($group['subject'] as $key => $lines) { ?>
              <tr>
                <td class="text-center" style="height: 100px;"><?php echo $no++;?></td>
                <td style="padding-left: 5px">
                  <?php echo $key;?>
                </td>
                <?php foreach ($lines['kategori'] as $key => $kat) { ?>
                  <td class="text-center" style="padding-left: 5px">
                    <?php echo $kat['full_score'];?>
                  </td>
                  <td class="text-center">
                    <?php echo $kat['grade'];?>
                  </td>
                  <td <?php if($kat['remark']=="-"){echo 'class="text-center"';} else {echo ' valign="top"';}?> style="padding: 5px;">
                    <?php echo $kat['remark'];?>
                  </td>
                <?php } ?>
              </tr>
              <?php } ?>
            <?php } ?>
          </tbody>
        </table>
        <h5 style="margin-top:20px;"><b>C. EXTRACURRICULAR</b></h5>
        <table border="1" width="100%">
          <col style="width:5%">
          <col style="width:20%">
          <thead>
            <tr>
              <th class="text-center">NO</th>
              <th class="text-center">SUBJECT</th>
              <th class="text-center">GRADE</th>
              <th class="text-center">REMARK</th>
            </tr>
          </thead>
        </table>
        <table border="0" width="100%" style="margin-top:20px;">
          <col style="width:50%">
          <col style="width:50%">
          <tr>
            <td style="padding-right: 5px;" valign="top">
              <h5><b>D. PERSONAL CHARACTER</b></h5>
              <table border="1" width="100%">
                <col style="width:10%">
                <col style="width:40%">
                <thead>
                  <tr>
                    <th class="text-center">NO</th>
                    <th class="text-center">ASPECT</th>
                    <th class="text-center">DESCRIPTION</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($personal->result() as $personal) { ?>
                  <tr>
                    <td class="text-center">1</td>
                    <td style="padding-left: 5px;">Conduct</td>
                    <td class="text-center"><?php echo $personal->conduct;?></td>
                  </tr>
                  <tr>
                    <td class="text-center">2</td>
                    <td style="padding-left: 5px;">Neatness & Uniform</td>
                    <td class="text-center"><?php echo $personal->neatness;?></td>
                  </tr>
                  <tr>
                    <td class="text-center">3</td>
                    <td style="padding-left: 5px;">Concentration</td>
                    <td class="text-center"><?php echo $personal->concentration;?></td>
                  </tr>
                  <tr>
                    <td class="text-center">4</td>
                    <td style="padding-left: 5px;">Reading Habits</td>
                    <td class="text-center"><?php echo $personal->reading;?></td>
                  </tr>
                  <tr>
                    <td class="text-center">5</td>
                    <td style="padding-left: 5px;">Helpfulness</td>
                    <td class="text-center"><?php echo $personal->helpfulness;?></td>
                  </tr>
                  <tr>
                    <td class="text-center">6</td>
                    <td style="padding-left: 5px;">Sosiability</td>
                    <td class="text-center"><?php echo $personal->sosiability;?></td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
            </td>
            <td style="padding-left: 5px;" valign="top">
              <h5><b>E. PHYSICAL CONDITION</b></h5>
              <table border="1" width="100%">
                <col style="width:10%">
                <col style="width:40%">
                <thead>
                  <tr>
                    <th class="text-center">NO</th>
                    <th class="text-center">ASPECT</th>
                    <th class="text-center">DESCRIPTION</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($physical->result() as $physical) { ?>
                  <tr>
                    <td class="text-center">1</td>
                    <td style="padding-left: 5px;">Head Circumference</td>
                    <td class="text-center"><?php echo $physical->head;?> Cm</td>
                  </tr>
                  <tr>
                    <td class="text-center">2</td>
                    <td style="padding-left: 5px;">Hearing</td>
                    <td class="text-center"><?php echo $physical->hearing;?></td>
                  </tr>
                  <tr>
                    <td class="text-center">3</td>
                    <td style="padding-left: 5px;">Height</td>
                    <td class="text-center"><?php echo $physical->height;?> Cm</td>
                  </tr>
                  <tr>
                    <td class="text-center">4</td>
                    <td style="padding-left: 5px;">Finger Nails</td>
                    <td class="text-center"><?php echo $physical->finger;?></td>
                  </tr>
                  <tr>
                    <td class="text-center">5</td>
                    <td style="padding-left: 5px;">Teeth</td>
                    <td class="text-center"><?php echo $physical->teeth;?></td>
                  </tr>
                  <tr>
                    <td class="text-center">6</td>
                    <td style="padding-left: 5px;">Vision</td>
                    <td class="text-center"><?php echo $physical->vision;?></td>
                  </tr>
                  <tr>
                    <td class="text-center">7</td>
                    <td style="padding-left: 5px;">Weight</td>
                    <td class="text-center"><?php echo $physical->weight;?></td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
            </td>
          </tr>
        </table>
        <h5 style="margin-top:20px;"><b>F. ATTENDANCE</b></h5>
        <table border="1" width="50%">
          <col style="width:10%">
          <col style="width:40%">
          <thead>
            <tr>
              <td class="text-center">1</td>
              <td style="padding-left: 5px;">Sick</td>
              <td class="text-center"><?php echo $sick;?> Days</td>
            </tr>
            <tr>
              <td class="text-center">2</td>
              <td style="padding-left: 5px;">Permit</td>
              <td class="text-center"><?php echo $permit;?> Days</td>
            </tr>
            <tr>
              <td class="text-center">3</td>
              <td style="padding-left: 5px;">Alpha</td>
              <td class="text-center"><?php echo $alpha;?> Days</td>
            </tr>
          </thead>
        </table>
        <h5 style="margin-top:30px;"><b>G. GENERAL REMARKS</b></h5>
        <table border="1" width="100%">
          <tr>
            <td valign="middle" class="text-center" style="padding: 10px;"><?php echo $remarks;?></td>
          </tr>
        </table>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <table border="0" width="100%" style="margin-top: 20px; ">
          <tr>
            <td class="text-center" width="50%">
              Samarinda, <?php echo date('d F Y',strtotime('2020-12-18'));?> 
              <br><br><br><br><br><br>
              <b>
                <u><?php echo $homeroom;?></u> 
              </b>
              <br>Homeroom Teacher  
            </td>
            <td class="text-center" width="50%">
              <br><br><br><br><br><br>
              <b>
                <u><?php echo $parent;?></u> 
              </b>
              <br>Parent
            </td>
          </tr>
          <tr>
            <td colspan="2" class="text-center">
            <br><br><br><br><br>
            <b>
              <u><?php echo $principal;?></u>
            </b>
            <br>Principal
          </td>
          </tr>
        </table>
      </div>
    </div>
	</div>
</body>
</html>