using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace MEA.Server.Entities
{
    public class ReferenceSequence
    {
        [Key]
        [DatabaseGenerated(DatabaseGeneratedOption.Identity)]
        public int Id { get; set; }

        [Required]
        [MaxLength(10)]
        public string EuPrefix { get; set; } = "AA";

        [Required]
        public int EuCurrentNumber { get; set; } = 0;

        [Required]
        [MaxLength(10)]
        public string NonEuPrefix { get; set; } = "AAA";

        [Required]
        public int NonEuCurrentNumber { get; set; } = 0;
    }
}
