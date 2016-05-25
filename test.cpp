#include <iostream>
#include <string>

#include "pugixml/src/pugixml.hpp"
#include "/Users/Andy/project/automata/include/thirdparty/json.hpp"

std::string urlDecode(const std::string &SRC) {
    std::string ret;
    char ch;
    int i, ii;
    for (i=0; i<SRC.length(); i++) {
        if (int(SRC[i])==37) {
            sscanf(SRC.substr(i+1,2).c_str(), "%x", &ii);
            ch=static_cast<char>(ii);
            ret+=ch;
            i=i+2;
        } else {
            ret+=SRC[i];
        }
    }
    return (ret);
}

int main(int argc, char** argv)
{
	std::string not_endode_names("REPORT_COMMIT_KEY|MH_APP_NOTIFICATION_DATA|APPEARED_CHALLENGE_FLOORS");
	pugi::xml_document doc;
	pugi::xml_parse_result result = doc.load_file("com.madhead.tos.zh.v2.playerprefs.xml");
		
	if (result)
	{
		for (pugi::xml_node map: doc.children("map"))
		{
			for (pugi::xml_node string: map.children("string"))
			{
				nlohmann::json json_value;
				std::string name(string.attribute("name").value());
				std::string decode_value = urlDecode(std::string(string.child_value()));
				if (decode_value.length() > 32 && not_endode_names.find(name) == std::string::npos)
				{
					decode_value = decode_value.substr(32);
				}
				if (name == "MH_CACHE_RUNTIME_DATA_CURRENT_FLOOR_ENTER_DATA")
				{
					decode_value = decode_value.substr(449);
				}
				else if (name == "PRE_BATTLE_INFO_COMPAREAPI_DATA_JSON")
				{
					decode_value = decode_value.substr(50);
				}
				else if (name == "PRE_BATTLE_INFO_COMPAREAPI_DATA_STAGE_JSON")
				{
					decode_value = decode_value.substr(55);
				}
				else if (name == "PRE_BATTLE_INFO_COMPAREAPI_DATA_FLOOR_JSON")
				{
					decode_value = decode_value.substr(55);
				}
				else if (name == "MH_CACHE_API_DATA_JSON")
				{
					decode_value = decode_value.substr(1);
				}
				try
				{
					json_value = nlohmann::json::parse(decode_value);
					std::cout << name << " : \n" << json_value.dump(4) << "\n" << std::endl;
				} catch (...) 
				{
					std::cout << name << " : " << decode_value << "\n" << std::endl;
				}
			}
		}
	}
	return 0;
}