import { Test, TestingModule } from '@nestjs/testing';
import { SpotsApiController } from './spots-api.controller';

describe('SpotsApiController', () => {
  let controller: SpotsApiController;

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      controllers: [SpotsApiController],
    }).compile();

    controller = module.get<SpotsApiController>(SpotsApiController);
  });

  it('should be defined', () => {
    expect(controller).toBeDefined();
  });
});
