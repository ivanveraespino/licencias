CREATE TRIGGER trg_representante_tipovia
ON representante
AFTER INSERT, UPDATE
AS
BEGIN
    SET NOCOUNT ON;

    -- Insertar en tipovia si no existe
    INSERT INTO tipovia (nombre)
    SELECT DISTINCT i.tipovia
    FROM inserted i
    WHERE i.tipovia IS NOT NULL
      AND NOT EXISTS (
          SELECT 1 
          FROM tipovia t 
          WHERE t.nombre = i.tipovia
      );
END;
GO
