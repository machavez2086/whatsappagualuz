import nltk
import urllib
import json
import os

class WhatsappAskProcessor(object):

    def getLemas(self, data):
        urllematizer = "http://127.0.0.1:5001/lematizerquery"
        lemastokens = nltk.tokenize.word_tokenize(self.elimina_stops(data.lower()))
        #lemastokens = nltk.tokenize.word_tokenize(data.lower())
        
        resultlemas = self.getPageUrllib(urllematizer, lemastokens)
        return " ".join(resultlemas)

    def getPageUrllib(self, url, data):
        params = json.dumps(data).encode('utf8')
        req = urllib.request.Request(url, data=params,
                                     headers={'content-type': 'application/json'})
        response = urllib.request.urlopen(req)
        data = json.loads(response.read().decode('utf8'))
        return data["array"]

    def elimina_stops(self, s):
        #stops = set(stopwords.words('spanish'))
        with open(os.path.join("lib", "stopwordsES")) as f:
        #with open("stopwordsES") as f:
            stops = f.read().split()
        f.closed
        return ' '.join((c for c in s.split(" ") if c not in stops))

