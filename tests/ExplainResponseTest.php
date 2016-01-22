<?php namespace Scriptotek\Sru;

use Mockery as m;

class ExplainResponseTest extends TestCase
{
    public function testNormalResponse()
    {
        $res = new ExplainResponse('<?xml version="1.0" encoding="UTF-8"?>
			<sru:explainResponse xmlns:sru="http://www.loc.gov/zing/srw/">
			  <sru:version>1.2</sru:version>
			  <sru:record>
			    <sru:recordPacking>xml</sru:recordPacking>
			    <sru:recordSchema>http://explain.z3950.org/dtd/2.0/</sru:recordSchema>
			    <sru:recordData>
			      <explain xmlns="http://explain.z3950.org/dtd/2.0/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://explain.z3950.org/dtd/2.0/ http://explain.z3950.org/dtd/zeerex-2.0.xsd">
			        <serverInfo protocol="SRU" transport="http">
			          <host>sru.bibsys.no</host>
			          <port>80</port>
			          <database>biblio</database>
			        </serverInfo>
			        <databaseInfo>
			          <title lang="en" primary="true">BIBSYS Union Catalogue</title>
			          <description lang="en">SRU access to the BIBSYS Union Catalogue.</description>
			          <contact lang="en">support@bibsys.no</contact>
			          <restrictions lang="en">It is prohibited to use this service for bulk downloading of records.</restrictions>
			          <links>
			            <link type="icon">http://www.bibsys.no/images/logo/bibsys_logo_medium.jpg</link>
			          </links>
			        </databaseInfo>
			        <metaInfo>
			          <dateModified>Sat Apr 26 16:47:43 CEST 2014</dateModified>
			        </metaInfo>
			        <indexInfo>
			          <set identifier="info:srw/cql-context-set/1/dc-v1.1" name="dc"/>
			          <set identifier="info:srw/cql-context-set/2/rec-1.1" name="rec"/>
			          <set identifier="info:srw/cql-context-set/15/norzig-1.1" name="norzig"/>
			          <set identifier="info:srw/cql-context-set/1/bib-v1" name="bib"/>
			          <set identifier="http://www.bibsys.no/context/bs/1.0" name="bs"/>
			          <set identifier="info:srw/cql-context-set/1/cql-v1.2" name="cql"/>
			          <index scan="false" search="true" sort="true">
			            <title>Identifiserer en bibliografisk enhet</title>
			            <map>
			              <name set="bs">objektid</name>
			            </map>
			          </index>
			          <index scan="false" search="true" sort="true">
			            <title>An unambiguous reference to the resource within a given context.</title>
			            <map>
			              <name set="dc">identifier</name>
			            </map>
			          </index>
			          <index scan="false" search="true" sort="true">
			            <title>nlm nummer</title>
			            <map>
			              <name set="bs">nlm</name>
			            </map>
			          </index>
			          <index scan="false" search="true" sort="true">
			            <title>institusjonsspesifikke emneord</title>
			            <map>
			              <name set="bs">ubtsy</name>
			            </map>
			          </index>
			          <index scan="false" search="true" sort="true">
			            <title>en plassering</title>
			            <map>
			              <name set="bs">geografisk-emneord</name>
			            </map>
			          </index>
			          <index scan="false" search="true" sort="true">
			            <title/>
			            <map>
			              <name set="bs">antallinstrumenter</name>
			            </map>
			          </index>
			          <index scan="false" search="true" sort="true">
			            <title>intern</title>
			            <map>
			              <name set="bs">issn-annen-manifestasjon</name>
			            </map>
			            <map>
			              <name set="bs">ismn-annen-manifestasjon</name>
			            </map>
			            <map>
			              <name set="bs">isbn-annen-manifestasjon</name>
			            </map>
			          </index>
			        </indexInfo>
			        <schemaInfo>
			          <schema identifier="info:srw/schema/1/dc-v1.1" name="dc"/>
			          <schema identifier="info:lc/xmlns/marcxchange-v1" name="marcxchange"/>
			        </schemaInfo>
			        <configInfo>
			          <default type="relation">=</default>
			          <default type="retrieveSchema">marcxchange</default>
			          <default type="numberOfRecords">10</default>
			          <setting type="maximumRecords">50</setting>
			        </configInfo>
			      </explain>
			    </sru:recordData>
			  </sru:record>
			</sru:explainResponse>');

        $this->assertEquals('1.2', $res->version);
        $this->assertEquals('sru.bibsys.no', $res->host);
        $this->assertEquals(80, $res->port);
        $this->assertEquals('biblio', $res->database->identifier);
        $this->assertEquals('BIBSYS Union Catalogue', $res->database->title);
        $this->assertEquals('SRU access to the BIBSYS Union Catalogue.', $res->database->description);
        $this->assertCount(7, $res->indexes);
        $this->assertFalse($res->indexes[0]->scan);
        $this->assertTrue($res->indexes[0]->sort);
        $this->assertTrue($res->indexes[0]->search);
        $this->assertEquals('Identifiserer en bibliografisk enhet', $res->indexes[0]->title);
        $this->assertEquals('bs.objektid', $res->indexes[0]->maps[0]);
    }
}
