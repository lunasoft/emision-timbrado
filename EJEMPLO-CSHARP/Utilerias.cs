using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace EJEMPLO_CSHARP
{
    public class Utilerias
    {
        public static string RemoverCaracteresInvalidosXml(string str)
        {
            str = str.Replace("\r\n", "");
            str = str.Replace("\r", "");
            str = str.Replace("\n", "");
            str = str.Replace(@"<?xml version=""1.0"" encoding=""utf-16""?>", @"<?xml version=""1.0"" encoding=""utf-8""?>").Trim();
            str = str.Replace("﻿", "");
            str = str.Replace(@"
", "");
            return str;
        }
    }
}
