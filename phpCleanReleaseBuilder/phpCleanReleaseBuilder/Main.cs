using System;
using System.Collections;
using System.Collections.Generic;
using System.IO;

namespace phpCleanReleaseBuilder
{
	class MainClass
	{
		public static void Main (string[] args)
		{
			string[] SourceFiles = File.ReadAllLines ("phpCleanReleaseList");
			string CmpFile = "";
			List<string> CompiledFile = new List<string> ();
			CmpFile += "<?php ";
			CompiledFile.Add("<?php");
			string VersionString = "UnKnown";
			foreach (string s in SourceFiles) {
				Console.Write("Reading File '" + s + "'...");
				try
				{
					string[] SourceLines = File.ReadAllLines(s);
					bool shouldWrite = false;
					foreach(string l in SourceLines)
					{
						if(l.Trim().ToLower().StartsWith("//#version"))
							VersionString = l.Trim().Split(new char[] { '='})[1].Trim();
						else
						{
							if(!shouldWrite && l.Trim().ToLower() == "//#begin_export")
								shouldWrite = true;
							else if(shouldWrite)
							{
								if(l.Trim().ToLower() == "//#end_export")
									shouldWrite = false;
								else
								{
									if(!l.Trim().StartsWith("//"))
									{
										CompiledFile.Add(l);
										CmpFile += l.Replace("\n", "").Replace("\r","").Replace("\t", " ").Trim() + " ";
									}
								}
							}
						}
					}
					Console.WriteLine("Success");
				}
				catch
				{
					Console.WriteLine("Failed");
				}
			}
			CmpFile += "?>";
			CompiledFile.Add ("?>");
			File.WriteAllLines("phpClean_" + VersionString + ".php", CompiledFile.ToArray());
			File.WriteAllText("phpClean_" + VersionString + ".single.php", CmpFile);
		}
	}
}
